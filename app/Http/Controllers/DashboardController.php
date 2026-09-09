<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Roles;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class DashboardController extends Controller
{
    /**
     * Display user listing.
     */
    public function index()
    {
        $users = User::with('role')->get();
        return view('admin_pages.users.index', compact('users'));
    }

    /**
     * Show form for creating a new user.
     */
    public function create()
    {
        $roles = Roles::all();
        return view('admin_pages.users.create', compact('roles'));
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(8)],
            'user_role'=> 'required|exists:roles,id',
            'active'   => 'required|boolean',
        ]);

        $user = User::create([
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'password'  => Hash::make($validated['password']),
            'user_role' => $validated['user_role'],
            'active'    => $validated['active'],
        ]);

        // Handle avatar upload
        if ($request->hasFile('avatar_file')) {
            $user->addMediaFromRequest('avatar_file')
                ->toMediaCollection('avatar');
        }

        return redirect()->route('users')->with('success', 'User created successfully.');
    }

    /**
     * Show form for editing the specified user.
     */
    public function edit(User $user)
    {
        $roles = Roles::all();
        return view('admin_pages.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email,' . $user->id,
            'password'  => ['nullable', 'confirmed', Password::min(8)],
            'user_role' => 'required|exists:roles,id',
            'active'    => 'required|boolean',
        ]);

        $data = [
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'user_role' => $validated['user_role'],
            'active'    => $validated['active'],
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        // Handle avatar upload
        if ($request->hasFile('avatar_file')) {
            $user->clearMediaCollection('avatar');
            $user->addMediaFromRequest('avatar_file')
                ->toMediaCollection('avatar');
        }

        // Handle avatar removal
        if ($request->input('remove_avatar')) {
            $user->clearMediaCollection('avatar');
        }

        return redirect()->route('users')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user)
    {
        // Prevent admin from deleting themselves
        if ($user->id === auth()->id()) {
            return redirect()->route('users')->with('error', 'You cannot delete your own account.');
        }

        $user->delete();
        return redirect()->route('users')->with('success', 'User deleted successfully.');
    }

    /**
     * JSON response for users (legacy AJAX endpoint).
     */
    public function getUsers()
    {
        $users = User::with('role')->get();
        return response()->json($users, 200);
    }
}
