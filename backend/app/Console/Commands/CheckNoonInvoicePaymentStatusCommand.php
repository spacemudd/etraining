<?php

namespace App\Console\Commands;

use App\Models\Back\Invoice;
use App\Services\NoonService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckNoonInvoicePaymentStatusCommand extends Command
{
    protected $signature = 'noon:check-payment-status
        {--ids=* : قائمة orderId للتحقق (اختياري)}
        {--days=30 : تحقق فقط من الفواتير المدفوعة خلال آخر X يوم بناءً على وقت الدفع (افتراضي: 30 يوم)}
        {--chunk=25 : حجم الدُفعة}
        {--dry : تشغيل تجريبي بدون حفظ}';

    protected $description = 'Check payment status from Noon for given invoices and update local DB';

    public function handle(): int
    {
        $ids   = (array) $this->option('ids');
        $days  = (int) $this->option('days');
        $chunk = (int) $this->option('chunk');
        $dry   = (bool) $this->option('dry');

        $this->info('بدء التحقق من حالة الدفع للفواتير...');
        $this->info("فترة التحقق: آخر {$days} يوم");

        // مصدر الفواتير: IDs صريحة أو من الداتابيز
        $invoiceNumbers = !empty($ids) ? $ids : [
            'b9bc2920-f21b-4b7b-92af-636a569354f5',
           
            // أضف المزيد من أرقام الفواتير هنا
        ];

        $total = count($invoiceNumbers);
        if ($total === 0) {
            $this->warn('لا توجد فواتير للتحقق.');
            return 0;
        }

        $this->info('عدد الفواتير: ' . $total);

        $successCount = 0;
        $errorCount = 0;
        $notFoundCount = 0;
        $notPaidCount = 0;

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        /** @var \App\Services\NoonService $noon */
        $noon = app(NoonService::class);

        foreach ($invoiceNumbers as $invoiceNumber) {
            try {
                // البحث عن الفاتورة في قاعدة البيانات باستخدام payment_reference_id
                $invoice = Invoice::where('payment_reference_id', $invoiceNumber)->first();

                if (!$invoice) {
                    $this->error("الفاتورة رقم {$invoiceNumber} غير موجودة في قاعدة البيانات");
                    $notFoundCount++;
                    $bar->advance();
                    continue;
                }

                // التحقق من أن الفاتورة غير مدفوعة مسبقاً
                if ($invoice->status == Invoice::STATUS_PAID) {
                    $this->warn("الفاتورة رقم {$invoiceNumber} مدفوعة مسبقاً");
                    $bar->advance();
                    continue;
                }

                $this->info("التحقق من الفاتورة رقم {$invoiceNumber}...");

                // البحث في جميع الطلبات للفاتورة الواحدة خلال آخر 30 يوم
                $paidOrderFound = false;
                $latestPaidOrder = null;
                $latestPaidAt = null;

                // محاولة البحث في مركزين مختلفين (Jasarah و Jisr)
                $centerIds = [5676, 0]; // Jasarah و Jisr
                
                foreach ($centerIds as $centerId) {
                    try {
                        $order = $noon->getOrder($invoiceNumber, $centerId);
                        
                        if ($order && isset($order->result)) {
                            $this->info("تم العثور على طلب في المركز {$centerId}");
                            
                            // التحقق من جميع المعاملات في الطلب
                            if (isset($order->result->transactions) && is_array($order->result->transactions)) {
                                foreach ($order->result->transactions as $transaction) {
                                    // التحقق من أن المعاملة ناجحة ومدفوعة
                                    if ($transaction->type === 'SALE' && $transaction->status === 'SUCCESS') {
                                        $transactionDate = Carbon::parse($transaction->createdAt ?? $order->result->order->createdAt);
                                        $thirtyDaysAgo = Carbon::now()->subDays($days);

                                        // التحقق من أن الدفع تم خلال آخر 30 يوم
                                        if ($transactionDate->gte($thirtyDaysAgo)) {
                                            $paidOrderFound = true;
                                            $latestPaidOrder = $order;
                                            $latestPaidAt = $transactionDate;
                                            
                                            $this->info("تم العثور على دفع ناجح بتاريخ: {$transactionDate->format('Y-m-d H:i:s')}");
                                            break 2; // الخروج من الحلقتين
                                        } else {
                                            $this->warn("الدفع أقدم من {$days} يوم: {$transactionDate->format('Y-m-d H:i:s')}");
                                        }
                                    }
                                }
                            }
                        }
                    } catch (\Exception $e) {
                        $this->warn("خطأ في المركز {$centerId}: " . $e->getMessage());
                        continue;
                    }
                }

                // إذا لم يتم العثور على دفع ناجح
                if (!$paidOrderFound) {
                    $this->warn("لم يتم العثور على دفع ناجح للفاتورة رقم {$invoiceNumber} خلال آخر {$days} يوم");
                    $notPaidCount++;
                    $bar->advance();
                    continue;
                }

                // تحديث الفاتورة في قاعدة البيانات
                DB::beginTransaction();
                try {
                    $updates = [
                        'status' => Invoice::STATUS_PAID,
                        'payment_method' => Invoice::PAYMENT_METHOD_CREDIT_CARD,
                        'paid_at' => $latestPaidAt,
                        'payment_detail_method' => data_get($latestPaidOrder, 'result.paymentDetails.mode'),
                        'payment_detail_brand' => data_get($latestPaidOrder, 'result.paymentDetails.brand'),
                    ];

                    // إزالة القيم الفارغة
                    $updates = array_filter($updates, function($value) {
                        return !is_null($value);
                    });

                    if (!$dry) {
                        $invoice->update($updates);
                    }

                    $successCount++;
                    DB::commit();
                    
                    $this->info("تم تحديث الفاتورة رقم {$invoiceNumber} إلى حالة مدفوعة بتاريخ: {$latestPaidAt->format('Y-m-d H:i:s')}");
                    
                } catch (\Throwable $e) {
                    DB::rollBack();
                    $this->error("فشل تحديث الفاتورة رقم {$invoiceNumber}: " . $e->getMessage());
                    $errorCount++;
                }

                // تهدئة بسيطة لتجنب تجاوز حدود API
                usleep(200000); // 200ms

            } catch (\Throwable $e) {
                $this->error("خطأ في معالجة الفاتورة رقم {$invoiceNumber}: " . $e->getMessage());
                $errorCount++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        $this->info('=== ملخص النتائج ===');
        $this->info("تم تحديث الفواتير إلى مدفوعة: {$successCount}");
        $this->info("الفواتير غير الموجودة: {$notFoundCount}");
        $this->info("الفواتير غير المدفوعة خلال آخر {$days} يوم: {$notPaidCount}");
        $this->info("عدد الأخطاء: {$errorCount}");

        return $errorCount > 0 ? 1 : 0;
    }
}
