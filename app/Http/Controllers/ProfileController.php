<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Resources\UserResource;
use App\Services\ImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;

class ProfileController extends Controller
{
    use AuthorizesRequests;

    public function profile(): UserResource
    {
        $user = Auth::user();
        $this->authorize('view', $user);

        $user->load(['roles.permissions', 'profile']);

        return new UserResource($user);
    }

    public function updateProfile(Request $request, ImageService $imageService): JsonResponse
    {
        $user = auth()->user();

        $this->authorize('update', $user);

        $profile = auth()->user()->profile;

        $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:60'],
            'biography' => ['nullable', 'string', 'min:3', 'max:60'],
            'receive_notifications' => ['nullable', 'in:true,false'],
            'profile_picture' => ['nullable', 'image', 'mimes:jpg,png,jpeg,webp', 'max:4096'],
        ]);

        $user->update($request->only(['name']));

        $profile->update($request->only('biography', 'profile_picture', 'receive_notifications'));

        if ($request->hasFile('profile_picture')) {
            $imageService->storeLocal(
                $profile,
                'profile_picture',
                $profile->user->name,
                $request->file('profile_picture')
            );
        }

        return response()->json(['message' => 'Profile Update']);
    }

    public function updatePassword(Request $request): JsonResponse
    {
        $user = auth()->user();
        $this->authorize('update', $user);

        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['error' => 'Current Password is Incorrect'], 403);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return response()->json(['message' => 'Password Updated']);
    }
}
