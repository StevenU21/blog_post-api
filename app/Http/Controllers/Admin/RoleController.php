<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    use AuthorizesRequests;

    public function __invoke(): JsonResponse
    {
        $this->authorize('viewAny', Role::class);

        $roles = Role::orderBy('id', 'asc')->pluck('name', 'id');

        return response()->json($roles);
    }
}
