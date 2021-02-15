<?php

namespace Database\Factories\Back;

use App\Models\Back\MonthlyInvoicingBatch;
use Illuminate\Database\Eloquent\Factories\Factory;

class MonthlyInvoicingBatchFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = MonthlyInvoicingBatch::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'invoices_date' => now()->startOfMonth(),
            'period_from' => now()->subMonth()->startOfMonth(),
            'period_to' => now()->subMonth()->endOfMonth(),
            'job_status' => MonthlyInvoicingBatch::JOB_STATUS_QUEUED,
            'status' => MonthlyInvoicingBatch::STATUS_DRAFT,
            'progress' => 0,
            'total'=> 1,
        ];
    }
}
