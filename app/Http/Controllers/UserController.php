<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class UserController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:view_users', only: ['index', 'show']),
            new Middleware('can:create_users', only: ['create', 'store']),
            new Middleware('can:edit_users', only: ['edit', 'update']),
            new Middleware('can:delete_users', only: ['destroy']),
        ];
    }

    public function index()
    {
        // Panggil relasi 'roles' bawaan Spatie, bukan 'role'
        $users = User::with('roles')->latest()->paginate(10);

        // Ambil semua nama role untuk dropdown di Vue
        $roles = Role::pluck('name');

        return Inertia::render('Users/Index', [
            'users' => $users,
            'roles' => $roles
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8',
            'role' => 'required|string|exists:roles,name', // Validasi input role
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => bcrypt($validated['password']),
            'is_active' => true,
        ]);

        // Pasang role ke user baru
        $user->assignRole($validated['role']);

        return redirect()->back()->with('message', 'User created successfully.');
    }

    public function destroy(User $user)
    {
        // Cegah user menghapus dirinya sendiri
        if (auth()->id() === $user->id) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        try {
            // Coba hapus usernya
            $user->delete();
            return redirect()->back()->with('message', 'Team member removed successfully.');

        } catch (\Illuminate\Database\QueryException $e) {
            // Tangkap error 23000 (Integrity constraint violation / Relasi nyangkut)
            if ($e->getCode() == "23000") {
                return redirect()->back()->with('error', 'Cannot delete this user because they are still assigned as a Project Manager or have pending tasks. Please reassign their projects first.');
            }

            // Tangkap error database lainnya
            return redirect()->back()->with('error', 'A database error occurred while trying to delete the user.');
        }
    }

    public function show(User $user)
    {
        return Inertia::render('Users/Show', [
            'user' => $user
        ]);
    }

    public function edit(User $user)
    {
        return Inertia::render('Users/Edit', [
            'user' => $user
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8',
            'role' => 'required|string|exists:roles,name',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => bcrypt($validated['password'])]);
        }

        // Sync role (hapus role lama, ganti yang baru)
        $user->syncRoles([$validated['role']]);

        return redirect()->back()->with('message', 'User updated successfully.');
    }
}
