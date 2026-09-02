<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->paginate(15);

        return Inertia::render('Users/Index', [
            'users' => $users
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->back()->with('message', 'Team member added successfully.');
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
            'password' => 'nullable|string|min:8',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->back()->with('message', 'Team member updated successfully.');
    }
}
