<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UserRequest;

class UserController extends Controller
{
    use AuthorizesRequests;

    public function index(): AnonymousResourceCollection
    {
        $this->authorize('viewAny', User::class);

        $users = User::with(['roles.permissions', 'profile'])->latest()->paginate(10);

        return UserResource::collection($users);
    }

    public function show(User $user): UserResource
    {
        $this->authorize('view', $user);

        $user->load(['roles.permissions', 'profile']);

        return new UserResource($user);
    }

    public function store(UserRequest $request): UserResource
    {
        $user = User::create($request->validated() + [
            'password' => Hash::make($request->password),
        ]);

        Profile::create([
            'user_id' => $user->id
        ]);

        $role = $request->input('role', 'reader');

        $user->assignRole($role);

        return response()->json([
            'message' => 'User Updated Successfully',
            'user' => new UserResource($user),
            'role' => $role,
        ]);
    }

    public function update(UserRequest $request, User $user): JsonResponse
    {
        $request->validate([
            'role' => 'required|exists:roles,name',
        ]);

        $user->update($request->validated());

        $role = $request->input('role');

        if (is_array($role)) {
            return response()->json([
                'message' => 'Only one role can be assigned at a time',
            ], 400);
        }

        $user->syncRoles($role);

        return response()->json([
            'message' => 'User Updated Successfully',
            'user' => new UserResource($user),
            'role' => $role,
        ]);
    }

    public function destroy(User $user): JsonResponse
    {
        $this->authorize('destroy', $user);

        $user->delete();

        return response()->json(['message' => 'Resource was Deleted'], 200);
    }
}
