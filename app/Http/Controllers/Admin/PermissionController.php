<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    use AuthorizesRequests;

    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Permission::class);

        $permissions = Permission::orderBy('id', 'asc')->pluck('name', 'id');

        return response()->json($permissions);
    }

    public function getUserPermissions(User $user): JsonResponse
    {
        $this->authorize('view', $user);

        $directPermissions = $user->getDirectPermissions()->pluck('name');
        $rolePermissions = $user->getPermissionsViaRoles()->pluck('name');

        return response()->json([
            'direct_permissions' => $directPermissions,
            'role_permissions' => $rolePermissions,
        ]);
    }

    public function assignPermission(Request $request, User $user): JsonResponse
    {
        $this->authorize('assignPermissions', Permission::class);

        $request->validate([
            'permission' => ['required', 'array'],
            'permission.*' => ['exists:permissions,name'],
        ]);

        $permissions = $request->input('permission');

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
            'permission' => ['required', 'array'],
            'permission.*' => ['exists:permissions,name'],
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
