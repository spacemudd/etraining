<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Http\Request;

/**
 * Pusher rejects socket IDs that are not exactly `\d+\.\d+`.
 * Duplicate X-Socket-ID headers arrive as "id, id" via $request->header().
 */
final class PusherSocketId
{
    public static function normalizeRequest(?Request $request = null): void
    {
        $request = $request ?: request();
        if (! $request) {
            return;
        }

        $raw = $request->header('X-Socket-ID');
        if (! is_string($raw) || trim($raw) === '') {
            return;
        }

        if (preg_match('/\d+\.\d+/', $raw, $matches) === 1) {
            $request->headers->set('X-Socket-ID', $matches[0]);

            return;
        }

        $request->headers->remove('X-Socket-ID');
    }
}
