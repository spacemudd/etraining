<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Listeners\UpdateUsersTimezoneSafely;
use Illuminate\Auth\Events\Login;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use JamesMills\LaravelTimezone\Listeners\Auth\UpdateUsersTimezone;
use Tests\CreatesApplication;

class UpdateUsersTimezoneSafelyTest extends BaseTestCase
{
    use CreatesApplication;

    public function test_container_resolves_vendor_listener_to_safe_implementation(): void
    {
        $this->assertInstanceOf(
            UpdateUsersTimezoneSafely::class,
            $this->app->make(UpdateUsersTimezone::class)
        );
    }

    public function test_skips_save_when_timezone_already_set(): void
    {
        $user = new class {
            public ?string $timezone = 'Asia/Riyadh';

            public bool $saved = false;

            public function save(): bool
            {
                $this->saved = true;

                return true;
            }
        };

        (new UpdateUsersTimezoneSafely())->handle(new Login('web', $user, false));

        $this->assertFalse($user->saved);
        $this->assertSame('Asia/Riyadh', $user->timezone);
    }

    public function test_sets_riyadh_when_timezone_is_missing(): void
    {
        $user = new class {
            public ?string $timezone = null;

            public bool $saved = false;

            public function save(): bool
            {
                $this->saved = true;

                return true;
            }
        };

        (new UpdateUsersTimezoneSafely())->handle(new Login('web', $user, false));

        $this->assertTrue($user->saved);
        $this->assertSame('Asia/Riyadh', $user->timezone);
    }
}
