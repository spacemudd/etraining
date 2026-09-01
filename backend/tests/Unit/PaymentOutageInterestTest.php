<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Back\Trainee;
use App\Models\PaymentOutageInterest;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Tests\CreatesApplication;

class PaymentOutageInterestTest extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('payment_outage_interest');
        Schema::create('payment_outage_interest', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('trainee_id');
            $table->uuid('invoice_id')->nullable();
            $table->timestamp('last_seen_at');
            $table->timestamp('notified_at')->nullable();
            $table->timestamps();
            $table->unique('trainee_id');
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('payment_outage_interest');
        parent::tearDown();
    }

    public function test_remembers_a_trainee_once(): void
    {
        $trainee = new Trainee();
        $trainee->id = (string) Str::uuid();

        PaymentOutageInterest::remember($trainee);
        PaymentOutageInterest::remember($trainee);

        $this->assertSame(1, PaymentOutageInterest::query()->count());
        $this->assertSame($trainee->id, PaymentOutageInterest::query()->first()->trainee_id);
    }

    public function test_ignores_null_trainee(): void
    {
        PaymentOutageInterest::remember(null);

        $this->assertSame(0, PaymentOutageInterest::query()->count());
    }
}
