<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()
            ->with(['roles'])
            ->where(function ($q) {
                $search = request('search');

                if ($search) {
                    return $q->where('name', 'like', "%$search%")
                        ->orWhere('email', 'like', "%$search%");
                }
            })
            ->get();

        return response()->json([
            'users' => $users,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required'],
            'email' => ['required'],
            'password' => ['required'],
            'role_id' => ['required', 'array'],
        ]);

        // return data user baru di create
        $user = User::create($validated);

        $user->roles()->sync([$validated->role_id]);

        return response()->json([
            'user' => $user,
        ]);
    }

    public function update(User $user, Request $request)
    {
        $validated = $request->validate([
            'name' => ['required'],
            'email' => ['required'],
        ]);

        $user->update($validated);

        if ($request->password) {
            $user->update(['password' => $request->password]);
        }

        return response()->json([
            'user' => $user,
        ]);
    }

    public function destroy($id)
    {
        User::destroy($id);

        return response()->json([
            'status' => 'deleted',
        ]);
    }
}
