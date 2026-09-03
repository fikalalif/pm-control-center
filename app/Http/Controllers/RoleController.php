<?php

namespace App\Http\Controllers;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class RoleController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:manage_roles'),
        ];
    }

    public function index()
    {
        // Ambil semua role beserta permission yang menempel padanya
        $roles = Role::with('permissions')->latest()->get();

        // Ambil semua daftar permission yang tersedia di sistem
        $permissions = Permission::all();

        return Inertia::render('Roles/Index', [
            'roles' => $roles,
            'permissions' => $permissions
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:roles,name|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        $role = Role::create(['name' => $validated['name']]);

        // Terapkan hak akses ke role baru (Poin 2 lu)
        if (!empty($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        return redirect()->back()->with('message', 'Role created successfully.');
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        $role->update(['name' => $validated['name']]);

        // Update ulang hak akses (Poin 2 lu)
        $role->syncPermissions($validated['permissions'] ?? []);

        return redirect()->back()->with('message', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        if ($role->name === 'Admin') {
            return redirect()->back()->with('error', 'Cannot delete the core Admin role.');
        }

        $role->delete();
        return redirect()->back()->with('message', 'Role deleted successfully.');
    }
}
