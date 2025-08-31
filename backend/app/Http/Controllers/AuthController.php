<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Student;
use App\Models\University;
use App\Models\Department;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    //
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => $user,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out']);
    }

    public function register(Request $request)
    {
        $v = $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|string|email|max:255|unique:users,email',
            'password'       => 'required|string|min:6',
            'university_id'  => 'required|exists:universities,id',
            'department_id'  => 'required|exists:departments,id',
        ]);

        // 1) email domain vs university.domain
        $emailDomain = strtolower(substr(strrchr($v['email'], '@'), 1));
        $university  = University::find($v['university_id']);

        if (! $university || ! $university->domain || strtolower($university->domain) !== $emailDomain) {
            return response()->json([
                'message' => 'Email domain must match the selected university.'
            ], 422);
        }

        // 2) department belongs to the selected university
        $deptOk = Department::where('id', $v['department_id'])
            ->where('university_id', $university->id)
            ->exists();

        if (! $deptOk) {
            return response()->json([
                'message' => 'Selected department does not belong to the chosen university.'
            ], 422);
        }

        // 3) create user + student atomically
        $result = DB::transaction(function () use ($v, $university) {
            $user = User::create([
                'name'     => $v['name'],
                'email'    => $v['email'],
                'password' => Hash::make($v['password']),
                'role'     => 'student', // remove if you don’t have a role column
            ]);

            Student::create([
                'user_id'       => $user->id,
                'university_id' => $v['university_id'],
                'department_id' => $v['department_id'],
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return compact('user', 'token');
        });

        return response()->json($result, 201);
    }
}
