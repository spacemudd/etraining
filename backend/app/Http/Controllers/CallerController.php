<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\MaqsamService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use RuntimeException;

class CallerController extends Controller
{
    public function __construct(private MaqsamService $maqsam)
    {
    }

    public function index(): Response
    {
        return Inertia::render('Caller/Index', [
            'configured' => $this->maqsam->isConfigured(),
            'agent_email' => auth()->user()->email,
        ]);
    }

    public function connect(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'nullable|email|max:255',
        ]);

        try {
            $email = $request->input('email') ?: auth()->user()->email;
            $token = $this->maqsam->generateAutologinToken($email);

            return response()->json([
                'url' => $this->maqsam->buildAutologinUrl($token),
            ]);
        } catch (RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function dial(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => 'required|string|max:30',
            'email' => 'nullable|email|max:255',
            'caller' => 'nullable|string|max:30',
        ]);

        try {
            $email = $request->input('email') ?: auth()->user()->email;
            $result = $this->maqsam->createCall(
                $email,
                $request->input('phone'),
                $request->input('caller')
            );

            return response()->json([
                'message' => __('words.caller-dial-started'),
                'call' => $result['call'] ?? null,
            ]);
        } catch (RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}
