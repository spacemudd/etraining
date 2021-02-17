<?php

namespace App\Http\Resources;

use App\Models\Back\MonthlyInvoicingBatch;
use App\Models\Back\SaleInvoice;
use Brick\Money\Money;
use Illuminate\Http\Resources\Json\JsonResource;

class MonthlyInvoicingBatchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     * @throws \Brick\Money\Exception\UnknownCurrencyException
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'invoices_date' => $this->invoices_date->format('d-m-Y'),
            'period_from' => $this->period_from->format('d-m-Y'),
            'period_to' => $this->period_to->format('d-m-Y'),
            'sale_invoices' => SaleInvoice::where('monthly_invoicing_batch_id', $this->id)->with(['billable' => function($q) {
                $q->with('company');
            }])->paginate(5)->onEachSide(1),
            'sale_invoices_count' => $this->sale_invoices_count,
            'sale_invoices_sum_grand_total' => $this->sale_invoices_sum_grand_total ? str_replace('SAR', '', Money::ofMinor($this->sale_invoices_sum_grand_total, 'SAR')->formatTo('en_SA')) : 0,
            'job_status' => $this->job_status,
            'job_status_display' => $this->job_status_display,
            'status' => $this->status,
            'is_draft' => $this->is_draft,
            'is_approved' => $this->is_approved,
            'is_processing' => $this->is_processing,
            'finished_sending_invoices' => $this->finished_sending_invoices,
            'finished_issuing_invoices' => $this->finished_issuing_invoices,
            'finished_generating_draft_invoices' => $this->finished_generating_draft_invoices,
            'status_display' => $this->status_display,
            'progress' => $this->progress,
            'total' => $this->total,
            'created_by_id' => $this->created_by_id,
            'created_by' => $this->created_by,
        ];
    }
}
