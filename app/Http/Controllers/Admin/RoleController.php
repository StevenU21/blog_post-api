<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    use AuthorizesRequests;

    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Role::class);

        $roles = Role::pluck('name', 'id');

        return response()->json($roles);
    }

    public function assignRole(Request $request, User $user): JsonResponse
    {
        $this->authorize('assignRole', Role::class);

        $request->validate([
            'role' => 'required|exists:roles,name',
        ]);

        $role = $request->input('role');

        if (is_array($role)) {
            return response()->json([
                'message' => 'Only one role can be assigned at a time',
            ], 400);
        }

        $user->syncRoles($role);

        return response()->json([
            'message' => 'Role assigned successfully',
            'user' => new UserResource($user),
            'role' => $role,
        ]);
    }
}
