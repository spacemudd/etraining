<?php

namespace App\Console\Commands;

use App\Models\Back\Invoice;
use App\Services\NoonService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class CheckDuplicateNoonPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'noon:check-duplicate-payments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check Noon for invoices that have been paid twice since October 1, 2025';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting duplicate payment check...');

        $startDate = Carbon::create(2025, 10, 1)->startOfDay();
        $this->info("Checking invoices created on or after: {$startDate->toDateString()}");

        // Query invoices from October 1, 2025
        $invoices = Invoice::withoutGlobalScopes()
            ->where('created_at', '>=', $startDate)
            ->get();

        $this->info("Found {$invoices->count()} invoices to check");

        if ($invoices->count() === 0) {
            $this->warn('No invoices found to check.');
            return 0;
        }

        $noonService = app()->make(NoonService::class);
        $duplicates = [];
        $bar = $this->output->createProgressBar($invoices->count());
        $bar->start();

        foreach ($invoices as $invoice) {
            try {
                $successfulOrders = $this->checkInvoiceForDuplicatePayments($invoice, $noonService);
                
                if (count($successfulOrders) > 1) {
                    foreach ($successfulOrders as $order) {
                        $duplicates[] = [
                            'invoice_id' => $invoice->id,
                            'order_id' => $order['order_id'],
                            'amount' => $order['amount'],
                            'date' => $order['date'],
                        ];
                    }
                }
            } catch (\Exception $e) {
                Log::error("Error checking invoice {$invoice->id}: " . $e->getMessage());
                // Continue processing other invoices
            }

            $bar->advance();
            
            // Rate limiting: 1 request per second
            sleep(1);
        }

        $bar->finish();
        $this->newLine(2);

        // Save results to file
        if (count($duplicates) > 0) {
            $this->saveResultsToFile($duplicates, $startDate);
            $this->info("Found " . count($duplicates) . " duplicate payment entries.");
        } else {
            $this->info("No duplicate payments found.");
        }

        return 0;
    }

    /**
     * Check if an invoice has been paid multiple times in Noon
     *
     * @param Invoice $invoice
     * @param NoonService $noonService
     * @return array Array of successful orders
     */
    private function checkInvoiceForDuplicatePayments(Invoice $invoice, NoonService $noonService): array
    {
        $successfulOrders = [];
        $centers = [5676, 0]; // Jasarah first, then Jisr

        foreach ($centers as $centerId) {
            try {
                $response = $noonService->getOrderByReference($invoice->id, $centerId);

                // Check if response is valid
                if (!$response || !isset($response->resultCode)) {
                    continue;
                }

                // Handle error codes (5021 = not found, 19089, 19001 = other errors)
                if ($response->resultCode === 5021 || $response->resultCode === 19089 || $response->resultCode === 19001) {
                    continue;
                }

                // Check if result exists
                if (!isset($response->result)) {
                    continue;
                }

                // Handle different response structures
                // Case 1: Single order object (result->order exists)
                if (isset($response->result->order)) {
                    $orderData = $response->result;
                    $this->processOrderForDuplicates($orderData, $invoice, $successfulOrders);
                }
                // Case 2: Array of orders (result is an array or result->orders exists)
                elseif (isset($response->result->orders) && is_array($response->result->orders)) {
                    foreach ($response->result->orders as $orderData) {
                        $this->processOrderForDuplicates($orderData, $invoice, $successfulOrders);
                    }
                }
                // Case 3: Result is directly an array of orders
                elseif (is_array($response->result)) {
                    foreach ($response->result as $orderData) {
                        $this->processOrderForDuplicates($orderData, $invoice, $successfulOrders);
                    }
                }

                // Continue checking other centers to find all orders for this invoice
                // An invoice might have orders in multiple centers
            } catch (\Exception $e) {
                // Log error but continue to next center
                Log::debug("Error checking center {$centerId} for invoice {$invoice->id}: " . $e->getMessage());
                continue;
            }
        }

        return $successfulOrders;
    }

    /**
     * Process an order to check for successful transactions
     *
     * @param object $orderData
     * @param Invoice $invoice
     * @param array &$successfulOrders
     * @return void
     */
    private function processOrderForDuplicates($orderData, Invoice $invoice, array &$successfulOrders): void
    {
        // Check if order has transactions
        if (!isset($orderData->order) || !isset($orderData->transactions)) {
            return;
        }

        // Check for successful transactions
        $transactions = is_array($orderData->transactions) 
            ? $orderData->transactions 
            : [$orderData->transactions];

        $hasSuccessfulTransaction = false;
        foreach ($transactions as $transaction) {
            if (isset($transaction->type) && 
                isset($transaction->status) &&
                $transaction->type === "SALE" &&
                $transaction->status === "SUCCESS") {
                
                $hasSuccessfulTransaction = true;
                break;
            }
        }

        // If this order has a successful transaction, add it to the list
        if ($hasSuccessfulTransaction) {
            $orderId = $orderData->order->id ?? null;
            $amount = $orderData->order->amount ?? $invoice->grand_total;
            
            // Try to get date from transaction timestamp, order creation date, or invoice paid_at
            $date = now()->toDateString();
            if (isset($orderData->order->creationTime)) {
                $date = Carbon::parse($orderData->order->creationTime)->toDateString();
            } elseif ($invoice->paid_at) {
                $date = $invoice->paid_at->toDateString();
            }

            $successfulOrders[] = [
                'order_id' => $orderId,
                'amount' => $amount,
                'date' => $date,
            ];
        }
    }

    /**
     * Save duplicate payment results to file
     *
     * @param array $duplicates
     * @param Carbon $startDate
     * @return void
     */
    private function saveResultsToFile(array $duplicates, Carbon $startDate): void
    {
        // Ensure directory exists
        $directory = 'invoice';
        if (!Storage::exists($directory)) {
            Storage::makeDirectory($directory);
        }

        $timestamp = now()->format('Y_m_d_His');
        $filename = "duplicate_payments_{$timestamp}.txt";
        $filepath = "{$directory}/{$filename}";

        // Build file content
        $content = "Duplicate Noon Payments Report\n";
        $content .= "Generated: " . now()->toDateTimeString() . "\n";
        $content .= "Date Range: From {$startDate->toDateString()}\n";
        $content .= "Total Duplicate Entries: " . count($duplicates) . "\n";
        $content .= str_repeat("=", 80) . "\n\n";
        $content .= sprintf("%-40s %-25s %-15s %-15s\n", "Invoice ID", "Order ID", "Amount", "Date");
        $content .= str_repeat("-", 80) . "\n";

        foreach ($duplicates as $duplicate) {
            $content .= sprintf(
                "%-40s %-25s %-15s %-15s\n",
                $duplicate['invoice_id'],
                $duplicate['order_id'] ?? 'N/A',
                number_format($duplicate['amount'], 2),
                $duplicate['date']
            );
        }

        Storage::put($filepath, $content);

        $fullPath = storage_path("app/{$filepath}");
        $this->info("Results saved to: {$fullPath}");
    }
}

