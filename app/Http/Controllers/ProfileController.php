<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    use AuthorizesRequests;
    
    public function profile(): UserResource
    {
        $user = Auth::user();

        $this->authorize('view', $user);

        $user->load(['roles.permissions']);

        return new UserResource($user);
    }
}
