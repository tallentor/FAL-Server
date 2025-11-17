<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\PendingUser;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Events\LawyerStatusUpdated;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


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
        'phone_number' => ['required', 'string', 'max:20', 'unique:users,phone_number', 'unique:pending_users,phone_number','regex:/^(0\d{9})$/'],
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


 public function loginTest(Request $request)
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
        $user->sendEmailVerificationNotification();

        return response()->json([
            'message' => 'Your email address is not verified. A new verification link has been sent to your email.'
        ], 403);
    }

    // Update last activity and mark as active if the user is a lawyer
    if ($user->role == 1) {
        $user->last_activity = now();
        $user->is_active = true;
        $user->save();

        // Optional: broadcast event for real-time frontend updates
        event(new \App\Events\LawyerStatusUpdated($user->id, 'active'));
    }

    // Create Sanctum token
    $token = $user->createToken($user->name)->plainTextToken;

    return response()->json([
        'message' => 'Login successful.',
        'user' => $user,
        'token' => $token
    ], 200);
}



    public function logoutExist(Request $request)
    {
        $request->user()->tokens()->delete();

        return [
            'message' => 'You are logged out'
        ];
    }


    public function logout(Request $request)
{
    $user = $request->user();

    if ($user && $user->role == 1) {
        $user->last_activity = null;
        $user->save();

        event(new LawyerStatusUpdated($user->id, 'inactive'));
    }

    // using sanctum tokens:
    if ($request->user()) {
        $request->user()->currentAccessToken()?->delete(); // or tokens()->delete() depending on implementation
    }

    return response()->json(['message' => 'Logged out']);
}



    // Get list of pending lawyers
    public function getPendingUsers(){

        return PendingUser::all();

    }

    public function getAllUsers(){
        return User::where('role' , 0)->get();
    }

    public function deleteUser(User $user)
{
    $user->delete();

    return response()->json([
        'message' => 'User deleted successfully'
    ]);
}


}
