<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmailVerificationController extends Controller
{
    /**
     * Verificar email
     */
    public function verify(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user || !$user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail) {
            return response()->json(['message' => 'User not authenticated or not eligible for verification.'], 403);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'The email has already been verified.'], 200);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return response()->json(['message' => 'Email verified successfully.'], 200);
    }

    /**
     * Reenviar email de verificación
     */
    public function resend(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user || !$user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail) {
            return response()->json(['message' => 'User not authenticated or not eligible for verification.'], 403);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'The email has already been verified.'], 200);
        }

        $user->sendEmailVerificationNotification();

        return response()->json(['message' => 'Verification email sent.'], 200);
    }
}
