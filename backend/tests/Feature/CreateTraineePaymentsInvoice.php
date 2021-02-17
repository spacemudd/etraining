<?php

namespace Tests\Feature;

use App\Actions\Fortify\CreateNewUser;
use App\Models\Back\Company;
use App\Models\Back\SaleInvoice;
use App\Models\Back\Trainee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateTraineePaymentsInvoice extends TestCase
{
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

    public function test_trainee_can_view_sale_invoice_page_for_payment()
    {
        $company = Company::factory()->create(['team_id' => $this->admin->current_team_id]);
        $trainee = Trainee::factory()->create([
            'team_id' => $this->admin->current_team_id,
            'company_id' => $company->id,
            'status' => Trainee::STATUS_APPROVED,
        ]);

        $invoice = SaleInvoice::factory([
            'team_id' => $this->admin->current_team_id,
            'billable_id' => $trainee->id,
            'billable_type' => Trainee::class,
            'number' => 'TI-2000',
            'status' => SaleInvoice::STATUS_ISSUED,
        ])->create();

        $this->get(route('sale-invoices.pay.bank-transfer', $invoice->id))
            ->assertSuccessful();
    }
}
