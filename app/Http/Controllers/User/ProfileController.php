<?php

namespace App\Http\Controllers\User;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class ProfileController extends Controller
{
    public function profileExist(Request $request)
    {
         $user = Auth::user();

         if (!$user) {
             return response()->json(['message' => 'Unauthenticated User .'], 401);
         }

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone_number' => $user->phone_number ?? null,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ], 200);
    }


    public function profile(Request $request)
{
    $user = Auth::user();

    if (!$user) {
        return response()->json(['message' => 'Unauthenticated User.'], 401);
    }

    return response()->json([
        'id' => $user->id,
        'name' => $user->name ?? null,
        'email' => $user->email ?? null,
        'phone_number' => $user->phone_number ?? null,
        'profile_image' => $user->profile_image ?? null,
        'address' => $user->address ?? null,
        'gender' => $user->gender ?? null,
        'married_status' => $user->married_status ?? null,
        'date_of_birth' => $user->date_of_birth ?? null,
        'passport_number' => $user->passport_number ?? null,
        'nationality' => $user->nationality ?? null,
        'created_at' => $user->created_at,
        'updated_at' => $user->updated_at,
    ], 200);
}




    public function updateProfileExist(Request $request)
{
    $user = $request->user(); // Authenticated user

    if (!$user) {
        return response()->json(['message' => 'Unauthenticated.'], 401);
    }

    $request->validate([
        'name' => ['sometimes', 'string', 'max:255'],
        'email' => ['sometimes', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
        'phone_number' => ['sometimes', 'string', 'max:20'],
        'profile_image' => ['sometimes', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'], // max 2MB
    ]);

    // Update user data
    if ($request->has('name')) {
        $user->name = $request->name;
    }
    if ($request->has('email')) {
        $user->email = $request->email;
    }
    if ($request->has('phone_number')) {
        $user->phone_number = $request->phone_number;
    }

    // Handle profile image upload
    if ($request->hasFile('profile_image')) {
        $file = $request->file('profile_image');
        $filename = time().'_'.$file->getClientOriginalName();
        $path = $file->storeAs('profile_images', $filename, 'public'); // stored in storage/app/public/profile_images

        // Delete old image if exists
        if ($user->profile_image) {
            Storage::disk('public')->delete($user->profile_image);
        }

        $user->profile_image = $path;
    }

    $user->save();

    return response()->json([
        'message' => 'Profile updated successfully',
        'user' => $user
    ], 200);
}



public function updateProfile12(Request $request)
{
    $user = $request->user();

    if (!$user) {
        return response()->json(['message' => 'Unauthenticated.'], 401);
    }

    $request->validate([
        'name' => ['sometimes', 'string', 'max:255'],
        'email' => ['sometimes', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
        'phone_number' => ['sometimes', 'string', 'max:20'],
        'profile_image' => ['sometimes', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        'address' => ['sometimes', 'string', 'max:500'],
        'gender' => ['sometimes', 'in:male,female,other'],
        'married_status' => ['sometimes', 'in:single,married,divorced,widowed'],
        'date_of_birth' => ['sometimes', 'date'],
        'passport_number' => ['sometimes', 'string', 'max:50'],
        'nationality' => ['sometimes', 'string', 'max:100'],
    ]);

    // Update basic fields
    $fields = [
        'name', 'email', 'phone_number', 'address',
        'gender', 'married_status', 'date_of_birth',
        'passport_number', 'nationality'
    ];

    foreach ($fields as $field) {
        if ($request->has($field)) {
            $user->$field = $request->$field;
        }
    }

    // Handle profile image upload
    if ($request->hasFile('profile_image')) {
        $file = $request->file('profile_image');
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('profile_images', $filename, 'public');

        // Delete old image if exists
        if ($user->profile_image) {
            Storage::disk('public')->delete($user->profile_image);
        }

        $user->profile_image = $path;
    }

    $user->save();

    return response()->json([
        'message' => 'Profile updated successfully',
        'user' => $user
    ], 200);
}


public function updateProfile(Request $request)
{
    $user = $request->user();

    if (!$user) {
        return response()->json(['message' => 'Unauthenticated.'], 401);
    }

    // Validate input
    $request->validate([
        'name' => 'sometimes|string|max:255',
        'email' => 'sometimes|string|email|max:255|unique:users,email,' . $user->id,
        'phone_number' => 'sometimes|string|max:20',
        'profile_image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
        'address' => 'sometimes|string|max:500',
        'gender' => 'sometimes|in:male,female,other',
        'married_status' => 'sometimes|in:single,married,divorced,widowed',
        'date_of_birth' => 'sometimes|date',
        'passport_number' => 'sometimes|string|max:50',
        'nationality' => 'sometimes|string|max:100',
    ]);

    // Update fields dynamically
    $updatableFields = [
        'name', 'email', 'phone_number', 'address',
        'gender', 'married_status', 'date_of_birth',
        'passport_number', 'nationality'
    ];

    foreach ($updatableFields as $field) {
        if ($request->has($field)) {
            $user->$field = $request->$field;
        }
    }

    // Handle profile image upload
    if ($request->hasFile('profile_image')) {
        $file = $request->file('profile_image');
        $filename = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
        $path = $file->storeAs('profile_images', $filename, 'public');

        // Delete old image if exists
        if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
            Storage::disk('public')->delete($user->profile_image);
        }

        $user->profile_image = $path;
    }

    $user->save();

    // Return full URL for frontend (React can directly display it)
    $user->profile_image_url = $user->profile_image
        ? url('storage/' . $user->profile_image)
        : null;

    return response()->json([
        'message' => 'Profile updated successfully',
        'user' => $user
    ], 200);
}






}