<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PendingUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function register1(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users|unique:pending_users',
            'phone_number' => ['required', 'string', 'max:20', 'unique:users,phone_number', 'unique:pending_users,phone_number'],
            'role' => ['required', Rule::in([0, 1, 2])],
            'password' => 'required|confirmed'
        ]);

        // If role == 1, send for approval
        if ($fields['role'] == 1) {
            $fields['password'] = Hash::make($fields['password']);

            PendingUser::create($fields);

            return response()->json([
                'message' => 'Your registration request has been submitted for approval. Please wait for admin approval.'
            ], 202);
        }

        // Otherwise, register immediately
        $fields['password'] = Hash::make($fields['password']);
        $user = User::create($fields);

        $token = $user->createToken($request->name);

        return [
            'user' => $user,
            'token' => $token->plainTextToken
        ];
    }

    public function register(Request $request)
{
    $fields = $request->validate([
        'name' => 'required',
        'email' => 'required|email|unique:users|unique:pending_users',
        'phone_number' => ['required', 'string', 'max:20', 'unique:users,phone_number', 'unique:pending_users,phone_number'],
        'role' => ['required', Rule::in([0, 1, 2])],
        'password' => 'required|confirmed'
    ]);

    // If role == 1, send to PendingUser for approval
    if ($fields['role'] == 1) {
        $fields['password'] = Hash::make($fields['password']);

        PendingUser::create($fields);

        return response()->json([
            'message' => 'Your registration request has been submitted for approval. Please wait for admin approval.'
        ], 202);
    }

    // Otherwise, register immediately
    $fields['password'] = Hash::make($fields['password']);
    $user = User::create($fields);

    // Send verification email
    $user->sendEmailVerificationNotification();

    // Create Sanctum token
    $token = $user->createToken($request->name)->plainTextToken;

    return response()->json([
        'message' => 'User registered successfully. A verification email has been sent to your email address.',
        'user' => $user,
        'token' => $token
    ], 201);
}

    public function approveUser($pendingUserId)
    {
        $pendingUser = PendingUser::findOrFail($pendingUserId);

        // Move to users table
        $user = User::create([
            'name' => $pendingUser->name,
            'email' => $pendingUser->email,
            'phone_number' => $pendingUser->phone_number,
            'role' => $pendingUser->role,
            'password' => $pendingUser->password,
        ]);

        // Remove from pending list
        $pendingUser->delete();

        return response()->json([
            'message' => 'User approved successfully.',
            'user' => $user
        ]);
    }

    public function rejectUser($pendingUserId)
    {
        $pendingUser = PendingUser::find($pendingUserId);

        if (!$pendingUser) {
            return response()->json([
                'message' => 'Pending user not found.'
            ], 404);
        }

        // Delete the pending user
        $pendingUser->delete();

        return response()->json([
            'message' => 'Pending user has been rejected and removed.'
        ]);
    }

    public function login1(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return [
                'errors' => [
                    'email' => ['The provided credentials are incorrect.']
                ]
            ];
        }

        $token = $user->createToken($user->name);

        return [
            'user' => $user,
            'token' => $token->plainTextToken
        ];
    }



    public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json([
            'message' => 'The provided credentials are incorrect.'
        ], 401);
    }

    // Check if email is verified
    if (!$user->hasVerifiedEmail()) {
        // Send another verification link automatically
        $user->sendEmailVerificationNotification();

        return response()->json([
            'message' => 'Your email address is not verified. A new verification link has been sent to your email.'
        ], 403);
    }

    // Create Sanctum token
    $token = $user->createToken($user->name)->plainTextToken;

    return response()->json([
        'message' => 'Login successful.',
        'user' => $user,
        'token' => $token
    ], 200);
}


    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return [
            'message' => 'You are logged out'
        ];
    }

    public function getPendingUsers(){

        return PendingUser::all();

    }
}
