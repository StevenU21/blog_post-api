<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    use AuthorizesRequests;

    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Permission::class);

        $permissions = Permission::orderBy('id', 'asc')->pluck('name', 'id');

        return response()->json($permissions);
    }

    public function assignPermission(Request $request, User $user): JsonResponse
    {
        $this->authorize('assignPermissions', Permission::class);

        $request->validate([
            'permission' => 'required|array',
            'permission.*' => 'exists:permissions,name',
        ]);

        $permissions = $request->input('permission');

        // Assign the new permissions
        $user->givePermissionTo($permissions);

        return response()->json(
            [
                'message' => 'Permissions granted successfully',
                'permissions' => $permissions,
            ]
        );
    }

    public function revokePermission(Request $request, User $user): JsonResponse
    {
        $this->authorize('revokePermissions', Permission::class);

        $request->validate([
            'permission' => 'required|array',
            'permission.*' => 'exists:permissions,name',
        ]);

        $permissions = $request->input('permission');
        $revokedPermissions = [];
        $rolePermissions = [];

        foreach ($permissions as $permission) {
            if ($user->hasDirectPermission($permission)) {
                $user->revokePermissionTo($permission);
                $revokedPermissions[] = $permission;
            } else {
                $rolePermissions[] = $permission;
            }
        }

        $message = 'Permissions revoked successfully';
        if (!empty($rolePermissions)) {
            $message .= '. The following permissions are inherited from roles and cannot be revoked: ' . implode(', ', $rolePermissions);
        }

        return response()->json(['message' => $message, 'revoked_permissions' => $revokedPermissions]);
    }
}
