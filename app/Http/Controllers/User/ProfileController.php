<?php

namespace App\Http\Controllers\User;

use Storage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function profile(Request $request)
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



    public function updateProfile(Request $request)
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

}