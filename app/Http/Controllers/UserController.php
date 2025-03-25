<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('first_name')->get();
        $count_users = $users->count();
        $count_admins = $users->whereIn('user_level', [0, 1])->count();
        $count_inactive = $users->where('user_status', 0)->count();

        return view('admin.users.index', compact('users', 'count_users', 'count_admins', 'count_inactive'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'user_level' => ['required', 'in:' . implode(',', array_keys(User::USERLEVELS))],
            'user_status' => ['required', 'in:' . implode(',', array_keys(User::USERSTATUS))],
        ]);

        $defaultPassword = 'password';

        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'],
            'password' => Hash::make($defaultPassword), // Default password
            'user_level' => $validated['user_level'],
            'user_status' => $validated['user_status'],
        ]);

        return redirect()->route('users.index')->with('success', 'User has been created.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'user_level' => ['required', 'in:'.implode(',', array_keys(User::USERLEVELS))],
            'user_status' => ['required', 'in:'.implode(',', array_keys(User::USERSTATUS))],
        ]);

        $updated_fields = [
            'user_level' => $validated['user_level'],
            'user_status' => $validated['user_status'],
        ];

        if (!empty($validated['password'])) {
            $updated_fields['password'] = $validated['password'];
        }

        $user->update($updated_fields);

        return redirect(route('users.index'))->with('success', 'User details have been updated.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect(route('users.index'))->with('success', 'User has been deleted');
    }
}
