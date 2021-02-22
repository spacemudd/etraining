<?php

namespace Tests\Feature;

use App\Actions\Fortify\CreateNewUser;
use App\Jobs\MakeTraineesDraftInvoicesJob;
use App\Models\Back\Company;
use App\Models\Back\CompanyContract;
use App\Models\Back\FinancialSetting;
use App\Models\Back\MonthlyInvoicingBatch;
use App\Models\Back\Payment;
use App\Models\Back\SaleInvoice;
use App\Models\Back\Trainee;
use App\Notifications\TraineeTrainingFeesNotification;
use Brick\Math\RoundingMode;
use Brick\Money\Money;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class CreateInvoicingTest extends TestCase
{
    use WithFaker;

    /**
     * An Admin user.
     *
     * @var \App\Models\User
     */
    private $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = (new CreateNewUser())->create([
            'name' => 'Shafiq al-Shaar',
            'email' => 'hello@getShafiq.com',
            'password' => 'hello123123',
            'password_confirmation' => 'hello123123',
        ]);

        $this->admin->current_team_id = $this->admin->personalTeam()->id;
        $this->admin->save();
    }

    public function test_monthly_invoicing_page_is_accessible()
    {
        $this->actingAs($this->admin)
            ->get(route('back.finance.invoicing.index'))
            ->assertSuccessful();
    }

    public function test_creating_a_monthly_invoicing_batch()
    {
        $this->actingAs($this->admin)
            ->get(route('back.finance.invoicing.create'))
            ->assertSuccessful();
    }

    public function test_storing_a_monthly_invoicing_batch()
    {
        $company = Company::factory()->create(['team_id' => $this->admin->current_team_id]);
        $contract = CompanyContract::factory()->create(['team_id' => $this->admin->current_team_id, 'company_id' => $company->id]);
        Trainee::factory()->create([
            'team_id' => $this->admin->current_team_id,
            'company_id' => $company->id,
            'status' => Trainee::STATUS_APPROVED,
        ]);

        $this->actingAs($this->admin)
            ->post(route('back.finance.invoicing.store'))
            ->assertSessionDoesntHaveErrors();

        $this->assertDatabaseHas('monthly_invoicing_batches', [
            'team_id' => $this->admin->current_team_id,
            'invoices_date' => now()->startOfMonth(),
            'period_from' => now()->subMonth()->startOfMonth(),
            'period_to' => now()->subMonth()->endOfMonth(),
            'job_status' => MonthlyInvoicingBatch::JOB_STATUS_COMPLETED,
            'status' => MonthlyInvoicingBatch::STATUS_DRAFT,
            'progress' => 1,
            'total'=> 1,
        ]);
    }

    public function test_viewing_monthly_invoicing_batch()
    {
        $savedBatch = MonthlyInvoicingBatch::factory([
            'team_id' => $this->admin->current_team_id,
            'invoices_date' => now()->startOfMonth(),
            'period_from' => now()->subMonth()->startOfMonth(),
            'period_to' => now()->subMonth()->endOfMonth(),
            'job_status' => MonthlyInvoicingBatch::JOB_STATUS_QUEUED,
            'status' => MonthlyInvoicingBatch::STATUS_DRAFT,
            'progress' => 0,
            'total'=> 0,
        ])->create();

        $this->actingAs($this->admin)
            ->get(route('back.finance.invoicing.show', $savedBatch->id))
            ->assertPropValue('batch', function($batch) use ($savedBatch) {
                $this->assertEquals($batch['id'], $savedBatch->id);
            });
    }

    public function test_storing_draft_batch_triggers_backend_job_to_make_trainee_invoices()
    {
        Queue::fake();

        $company = Company::factory()->create(['team_id' => $this->admin->current_team_id]);
        $contract = CompanyContract::factory()->create(['team_id' => $this->admin->current_team_id, 'company_id' => $company->id]);
        Trainee::factory()->create([
            'team_id' => $this->admin->current_team_id,
            'company_id' => $company->id,
            'status' => Trainee::STATUS_APPROVED,
        ]);

        $this->actingAs($this->admin)
            ->post(route('back.finance.invoicing.store'))
            ->assertSessionDoesntHaveErrors();

        $batch = MonthlyInvoicingBatch::first();

        Queue::assertPushed(function(MakeTraineesDraftInvoicesJob $job) use ($batch) {
            return $job->batch->id === $batch->id;
        });
    }

    public function test_backend_job_is_creating_draft_invoices_for_all_trainees()
    {
        $company = Company::factory()->create(['team_id' => $this->admin->current_team_id]);
        $contract = CompanyContract::factory()->create([
            'team_id' => $this->admin->current_team_id,
            'company_id' => $company->id,
        ]);
        $trainee = Trainee::factory()->create([
            'team_id' => $this->admin->current_team_id,
            'company_id' => $company->id,
            'status' => Trainee::STATUS_APPROVED,
        ]);

        $this->actingAs($this->admin)
            ->post(route('back.finance.invoicing.store'))
            ->assertSessionDoesntHaveErrors();

        $cost_per_month = FinancialSetting::where('team_id', $this->admin->current_team_id)->firstOrFail()
            ->getRawOriginal('trainee_monthly_subscription');

        $this->assertDatabaseHas('sale_invoices', [
            'team_id' => $this->admin->current_team_id,
            'issued_at' => now()->startOfMonth()->startOfDay(),
            'billable_id' => $trainee->id,
            'billable_type' => Trainee::class,
            'sub_total' => $cost_per_month,
            'tax_total' => Money::ofMinor($cost_per_month, 'SAR')
                ->multipliedBy(0.15, RoundingMode::HALF_UP)->getMinorAmount()->toInt(),
            'grand_total' => Money::ofMinor($cost_per_month, 'SAR')
                ->multipliedBy(1.15, RoundingMode::HALF_UP)->getMinorAmount()->toInt(),
    ]);
    }

    public function test_invoices_are_shown_for_the_batch()
    {
        $company = Company::factory()->create(['team_id' => $this->admin->current_team_id]);
        $trainee = Trainee::factory()->create([
            'team_id' => $this->admin->current_team_id,
            'company_id' => $company->id,
            'status' => Trainee::STATUS_APPROVED,
        ]);

        $this->actingAs($this->admin)
            ->post(route('back.finance.invoicing.store'))
            ->assertSessionDoesntHaveErrors();

        $savedBatch = MonthlyInvoicingBatch::withCount('sale_invoices')->first();

        $this->actingAs($this->admin)
            ->get(route('back.finance.invoicing.show', $savedBatch->id))
            ->assertPropValue('batch', function($batch) use ($savedBatch) {
                $this->assertEquals($savedBatch->sale_invoices->count(), $batch['sale_invoices_count']);
            });
    }

    public function test_approving_an_invoice_batch()
    {
        $company = Company::factory()->create(['team_id' => $this->admin->current_team_id]);
        $trainee = Trainee::factory()->create([
            'team_id' => $this->admin->current_team_id,
            'company_id' => $company->id,
            'status' => Trainee::STATUS_APPROVED,
        ]);

        $this->actingAs($this->admin)
            ->post(route('back.finance.invoicing.store'))
            ->assertSessionDoesntHaveErrors();

        $savedBatch = MonthlyInvoicingBatch::withCount('sale_invoices')->first();

        $this->actingAs($this->admin)
            ->post(route('back.finance.invoicing.approve', $savedBatch->id), ['approved' => true])
            ->assertSessionDoesntHaveErrors()
            ->assertRedirect(route('back.finance.invoicing.show', $savedBatch->id));

        $this->assertDatabaseHas('monthly_invoicing_batches', [
            'id' => $savedBatch->id,
            'status' => MonthlyInvoicingBatch::STATUS_APPROVED,
        ]);
    }

    public function test_approving_an_invoice_batch_creates_invoice_numbers()
    {
        $company = Company::factory()->create(['team_id' => $this->admin->current_team_id]);
        $trainee = Trainee::factory()->create([
            'team_id' => $this->admin->current_team_id,
            'company_id' => $company->id,
            'status' => Trainee::STATUS_APPROVED,
        ]);

        $this->actingAs($this->admin)
            ->post(route('back.finance.invoicing.store'))
            ->assertSessionDoesntHaveErrors();

        $savedBatch = MonthlyInvoicingBatch::withCount('sale_invoices')->first();

        $this->actingAs($this->admin)
            ->post(route('back.finance.invoicing.approve', $savedBatch->id), ['approved' => true])
            ->assertSessionDoesntHaveErrors()
            ->assertRedirect(route('back.finance.invoicing.show', $savedBatch->id));

        $this->assertDatabaseHas('monthly_invoicing_batches', [
            'id' => $savedBatch->id,
            'status' => MonthlyInvoicingBatch::STATUS_APPROVED,
        ]);

        $this->assertDatabaseHas('sale_invoices', [
            'number' => 'TI-021001',
        ]);
    }

    public function test_notification_is_sent_to_trainee_about_training_fees()
    {
        Notification::fake();

        $company = Company::factory()->create(['team_id' => $this->admin->current_team_id]);
        $trainee = Trainee::factory()->create([
            'team_id' => $this->admin->current_team_id,
            'company_id' => $company->id,
            'status' => Trainee::STATUS_APPROVED,
        ]);

        $this->actingAs($this->admin)
            ->post(route('back.finance.invoicing.store'))
            ->assertSessionDoesntHaveErrors();

        $savedBatch = MonthlyInvoicingBatch::withCount('sale_invoices')->first();

        $this->actingAs($this->admin)
            ->post(route('back.finance.invoicing.approve', $savedBatch->id), ['approved' => true])
            ->assertSessionDoesntHaveErrors()
            ->assertRedirect(route('back.finance.invoicing.show', $savedBatch->id));

        Notification::assertSentTo($trainee, TraineeTrainingFeesNotification::class);
    }

    public function test_trainee_uploading_their_proof_of_payment_as_bank_transfer()
    {
        $company = Company::factory()->create(['team_id' => $this->admin->current_team_id]);
        $trainee = Trainee::factory()->create([
            'team_id' => $this->admin->current_team_id,
            'company_id' => $company->id,
            'status' => Trainee::STATUS_APPROVED,
        ]);
        $sale_invoice = SaleInvoice::factory([
            'team_id' => $this->admin->current_team_id,
            'billable_id' => $trainee->id,
            'billable_type' => Trainee::class,
            'number' => rand(),
            'status' => SaleInvoice::STATUS_ISSUED,
            'sub_total' => 100,
            'tax_total' => 15,
            'grand_total' => 115,
        ])->create();

        $this->post(route('sale-invoices.pay.bank-transfer.transfer-receipt', $sale_invoice->id), [
            'bank_receipt' => $file = UploadedFile::fake()->create('receipt.png', 1024 * 24),
            'amount_transferred' => 115,
            'sender_name' => 'Shafiq',
            'sender_bank' => 'Palestine Bank',
        ]);

        $this->assertDatabaseHas('payments', [
            'team_id' => $this->admin->current_team_id,
            'sale_invoice_id' => $sale_invoice->id,
            'amount' => 115 * 100,
            'status' => Payment::STATUS_UNDER_REVIEW,
            'confirmed_at' => null,
            'confirmed_by' => null,
        ]);

        $this->assertEquals(1, $sale_invoice->payments()->count());
    }

    public function test_viewing_payments_that_require_manual_verification()
    {
        $company = Company::factory()->create(['team_id' => $this->admin->current_team_id]);
        $trainee = Trainee::factory()->create([
            'team_id' => $this->admin->current_team_id,
            'company_id' => $company->id,
            'status' => Trainee::STATUS_APPROVED,
        ]);
        $sale_invoice = SaleInvoice::factory([
            'team_id' => $this->admin->current_team_id,
            'billable_id' => $trainee->id,
            'billable_type' => Trainee::class,
            'number' => rand(),
            'status' => SaleInvoice::STATUS_ISSUED,
            'sub_total' => 100,
            'tax_total' => 15,
            'grand_total' => 115,
        ])->create();

        $this->post(route('sale-invoices.pay.bank-transfer.transfer-receipt', $sale_invoice->id), [
            'bank_receipt' => $file = UploadedFile::fake()->create('receipt.png', 1024 * 24),
            'amount_transferred' => 115,
            'sender_name' => 'Shafiq',
            'sender_bank' => 'Bahrain Bank',
        ]);

        $payment = Payment::first();

        $this->actingAs($this->admin)
            ->get(route('back.finance.payments.index'))
            ->assertPropValue('payments', function($payments) use ($payment) {
                $this->assertEquals($payment->id, $payments['data'][0]['id']);
            });
    }

    //public function test_approving_an_invoicing_batch()
    //{
    //    $savedBatch = MonthlyInvoicingBatch::factory([
    //        'team_id' => $this->admin->current_team_id,
    //        'invoices_date' => now()->startOfMonth(),
    //        'period_from' => now()->subMonth()->startOfMonth(),
    //        'period_to' => now()->subMonth()->endOfMonth(),
    //        'job_status' => MonthlyInvoicingBatch::JOB_STATUS_QUEUED,
    //        'status' => MonthlyInvoicingBatch::STATUS_DRAFT,
    //        'progress' => 0,
    //        'total'=> 1,
    //    ])->create();
    //
    //    $this->actingAs($this->admin)
    //        ->post(route('back.finance.invoicing.approve', $savedBatch->id))
    //        ->assertSessionDoesntHaveErrors();
    //
    //    $this->assertDatabaseHas('monthly_invoicing_batches', [
    //        'id' => $savedBatch->id,
    //        'status' => MonthlyInvoicingBatch::STATUS_APPROVED,
    //    ]);
    //}

    //public function test_monthly_training_fees_are_invoiced_to_trainees()
    //{
    //    // Make one company to hold the contract.
    //    $company = Company::factory()->create([
    //        'team_id' => $this->admin->current_team_id,
    //    ]);
    //
    //    // The contract to be assigned for the trainees.
    //    $contract = $company->contracts()->save(
    //        CompanyContract::factory()->make(['team_id' => $company->team_id])
    //    );
    //
    //    // Make a trainee.
    //    $trainee = Trainee::factory()->create([
    //        'team_id' => $this->admin->current_team_id,
    //        'company_id' => $company->id,
    //    ]);
    //
    //    // We're gonna get all the trainees under the contract and do this month's billing.
    //    // Now, we're going to set the monthly invoicing period
    //    // Question: What is the period of the invoice? It is for the past period.
    //    // E.g. If a student joins in the middle of the month, then at the end of the month
    //    // They're charged alongside the other people in the contract.
    //
    //    app()->make(MonthlyInvoicingService::class)
    //        ->generateTraineesMonthlyInvoices();
    //}
}
