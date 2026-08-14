<?php

declare(strict_types=1);

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\PushSubscription;
use App\Services\ChatWebPushService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatPushSubscriptionController extends Controller
{
    public function __construct(private readonly ChatWebPushService $pushService)
    {
        $this->middleware('can:access-whatsapp-chats');
    }

    public function vapidPublicKey(): JsonResponse
    {
        $key = $this->pushService->publicKey();
        if (! $key) {
            return response()->json([
                'configured' => false,
                'public_key' => null,
            ]);
        }

        return response()->json([
            'configured' => true,
            'public_key' => $key,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        if (! $this->pushService->isConfigured()) {
            return response()->json(['message' => 'Push notifications are not configured'], 503);
        }

        $validated = $request->validate([
            'endpoint' => 'required|string|max:500',
            'public_key' => 'nullable|string|max:255',
            'auth_token' => 'nullable|string|max:255',
            'content_encoding' => 'nullable|string|max:32',
        ]);

        $subscription = PushSubscription::query()->updateOrCreate(
            ['endpoint' => $validated['endpoint']],
            [
                'user_id' => $request->user()->id,
                'public_key' => $validated['public_key'] ?? null,
                'auth_token' => $validated['auth_token'] ?? null,
                'content_encoding' => $validated['content_encoding'] ?? 'aesgcm',
            ]
        );

        return response()->json([
            'id' => $subscription->id,
            'endpoint' => $subscription->endpoint,
        ], 201);
    }

    public function destroy(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'endpoint' => 'required|string|max:500',
        ]);

        PushSubscription::query()
            ->where('user_id', $request->user()->id)
            ->where('endpoint', $validated['endpoint'])
            ->delete();

        return response()->json(['ok' => true]);
    }
}
