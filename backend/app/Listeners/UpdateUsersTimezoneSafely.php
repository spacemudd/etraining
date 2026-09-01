<?php

declare(strict_types=1);

namespace App\Listeners;

use Illuminate\Auth\Events\Login;

/**
 * Replaces the vendor laravel-timezone login listener.
 *
 * That listener always called ip-api.com (90s curl timeout) before checking
 * whether the user already had a timezone. During morning login waves that
 * saturated PHP-FPM and timed the site out.
 */
class UpdateUsersTimezoneSafely
{
    private const DEFAULT_TIMEZONE = 'Asia/Riyadh';

    /**
     * @param  mixed  $event
     */
    public function handle($event): void
    {
        if (! $event instanceof Login) {
            return;
        }

        $user = $event->user;
        if ($user === null) {
            return;
        }

        if (filled($user->timezone ?? null)) {
            return;
        }

        $user->timezone = self::DEFAULT_TIMEZONE;
        $user->save();
    }
}
