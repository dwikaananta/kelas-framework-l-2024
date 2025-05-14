<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Str;

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

    public function show($id)
    {
        $user = User::find($id);

        return response()->json([
            'user' => $user,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required'],
            'email' => ['required'],
            'password' => ['required'],
            'profile' => ['nullable', 'file', 'mimes:png,jpg'],
            // 'role_id' => ['required', 'array'],
        ]);

        if ($request->file('profile')) {
            // store image to storage
            $extension = $request->file('profile')->extension();
            $file_name = Str::random(20) . '.' . $extension;

            $request->file('profile')->storeAs('users/profile', $file_name, "public");
        }

        // return data user baru di create
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'profile' => $file_name ?? null,
        ]);

        // $user->roles()->sync([$validated->role_id]);

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
