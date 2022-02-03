<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Back\Company;
use App\Models\Back\Invoice;
use Brick\Math\RoundingMode;
use Brick\Money\Context\CustomContext;
use Brick\Money\Money;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use PDF;

class FinancialInvoicesController extends Controller
{
    public function index()
    {
        $this->authorize('issue-monthly-invoices');

        return Inertia::render('Back/Finance/Invoices/Index', [
            'invoices' => Invoice::latest()->paginate(20),
        ]);
    }

    public function show(string $invoice_id)
    {
        $this->authorize('issue-monthly-invoices');

        $invoice = Invoice::query()
            ->with([
                'items',
                'company',
                'trainee',
            ])
            ->findOrFail($invoice_id);

        return Inertia::render('Back/Finance/Invoices/Show', [
            'invoice' => $invoice,
        ]);
    }

    public function expectedAmountPerInvoice(Request $request)
    {
        $request->validate([
            'from_date' => 'required',
            'to_date' => 'required',
            'company_id' => 'required|exists:companies,id',
        ]);

        $company = Company::findOrFail($request->company_id);

        // TODO: account for cases where its spanning onto the next month.
        // (e.g. the daily charge for a month of 31 days is different from one with 28 days)
        $from_date = Carbon::parse($request->from_date)->startOfDay();
        $to_date = Carbon::parse($request->to_date)->endOfDay();

        $days_to_charge = $from_date->diffInDays($to_date) + 1;

        $cost = Money::of($company->monthly_subscription_per_trainee, 'SAR', new CustomContext(5), RoundingMode::HALF_UP)
            ->dividedBy($from_date->daysInMonth, RoundingMode::HALF_UP)
            ->multipliedBy($days_to_charge)
            ->to(new CustomContext(2), RoundingMode::HALF_UP)
            ->getAmount()
            ->toFloat();

        return response()->json([
            'cost' => $cost,
        ]);
    }

    public function pdf($id)
    {
        $invoice = Invoice::findOrFail($id);
        $pdf = PDF::setOption('footer-html', resource_path('views/pdf/invoices/client-invoice-footer.html'))
            ->setOption('margin-bottom', 30)
        ->loadView('pdf.invoices.show', [
            'title' => 'Invoice',
            'invoice' => $invoice,
        ]);
        return $pdf->inline();
    }
}
