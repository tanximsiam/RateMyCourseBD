<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Student;


class StudentController extends Controller
{
    //
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|min:6|confirmed',
            'university_id' => 'required|exists:universities,id',
            'department_id' => 'required|exists:departments,id',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => 'student',
        ]);

        Student::create([
            'user_id'       => $user->id,
            'university_id' => $validated['university_id'],
            'department_id' => $validated['department_id'],
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => $user,
        ]);
    }

    public function me(Request $request)
    {
        $user = $request->user()->load('student.university', 'student.department');

        return response()->json([
            'user' => $user,
        ]);
    }
}
