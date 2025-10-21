<?php

namespace App\Http\Controllers;

use App\Models\LawyerProfile;
use Illuminate\Http\Request;



class LawyerProfileController extends Controller
{
    
    public function index()
    {
        // Return all lawyer profiles
        return LawyerProfile::all();
    }

   
    public function store(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'cover_image' => 'required|file|image|max:2048',
            'profile_image' => 'required|file|image|max:2048',
            'total_cases' => 'required|integer',
            'earning' => 'required|string',
            'rating' => 'required|numeric',
            'new_cases' => 'required|integer',
            'verified' => 'boolean',
        ]);

        // Store images in public/images/Lawyer folder
        $coverImagePath = $request->file('cover_image')->store('images/Lawyer', 'public');
        $profileImagePath = $request->file('profile_image')->store('images/Lawyer', 'public');

        $validated['cover_image'] = $coverImagePath;
        $validated['profile_image'] = $profileImagePath;

        $profile = LawyerProfile::create($validated);
        return response()->json($profile, 201);
    }

    
    public function show(LawyerProfile $lawyerProfile)
    {
        // Show a specific lawyer profile
        return $lawyerProfile;
    }

   
    public function update(Request $request, LawyerProfile $lawyerProfile)
    {
        // Validate input
        $validated = $request->validate([
            'cover_image' => 'file|image|max:2048',
            'profile_image' => 'file|image|max:2048',
            'total_cases' => 'integer',
            'earning' => 'string',
            'rating' => 'numeric',
            'new_cases' => 'integer',
            'verified' => 'boolean',
        ]);

        // Update images if present
        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('images/Lawyer', 'public');
        }
        if ($request->hasFile('profile_image')) {
            $validated['profile_image'] = $request->file('profile_image')->store('images/Lawyer', 'public');
        }

        $lawyerProfile->update($validated);
        return response()->json($lawyerProfile);
    }

   
    public function destroy(LawyerProfile $lawyerProfile)
    {
        // Delete the lawyer profile
        $lawyerProfile->delete();
        return response()->json(['message' => 'Profile deleted']);
    }
}
