<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LawyerProfile;
use Illuminate\Support\Facades\Auth;

class LawyerProfileController extends Controller
{
    public function index1(Request $request)
{
    $query = LawyerProfile::query();

    // Filter by minimum total cases
    if ($request->has('min_cases')) {
        $query->where('total_cases', '>=', $request->min_cases);
    }

    // Filter by maximum total cases
    if ($request->has('max_cases')) {
        $query->where('total_cases', '<=', $request->max_cases);
    }

    // Filter by minimum rating
    if ($request->has('min_rating')) {
        $query->where('rating', '>=', $request->min_rating);
    }

    // Filter by maximum rating
    if ($request->has('max_rating')) {
        $query->where('rating', '<=', $request->max_rating);
    }

    // Filter by verified status
    if ($request->has('verified')) {
        $verified = filter_var($request->verified, FILTER_VALIDATE_BOOLEAN);
        $query->where('verified', $verified);
    }

    // Sorting
    if ($request->has('sort_by')) {
        $sortField = $request->get('sort_by');
        $sortOrder = $request->get('sort_order', 'desc');

        if (in_array($sortField, ['total_cases', 'rating'])) {
            $query->orderBy($sortField, $sortOrder);
        }
    }

    // pagination
    if ($request->has('paginate')) {
        $perPage = $request->get('paginate', 10);
        $lawyers = $query->paginate($perPage);
    } else {
        $lawyers = $query->get();
    }

    return response()->json($lawyers);
}


    public function index(Request $request)
{
    $query = LawyerProfile::with('user:id,name');

    // Filter by minimum total cases
    if ($request->has('min_cases')) {
        $query->where('total_cases', '>=', $request->min_cases);
    }

    // Filter by maximum total cases
    if ($request->has('max_cases')) {
        $query->where('total_cases', '<=', $request->max_cases);
    }

    // Filter by minimum rating
    if ($request->has('min_rating')) {
        $query->where('rating', '>=', $request->min_rating);
    }

    // Filter by maximum rating
    if ($request->has('max_rating')) {
        $query->where('rating', '<=', $request->max_rating);
    }

    // Filter by verified status
    if ($request->has('verified')) {
        $verified = filter_var($request->verified, FILTER_VALIDATE_BOOLEAN);
        $query->where('verified', $verified);
    }

    // Sorting
    if ($request->has('sort_by')) {
        $sortField = $request->get('sort_by');
        $sortOrder = $request->get('sort_order', 'desc');

        if (in_array($sortField, ['total_cases', 'rating'])) {
            $query->orderBy($sortField, $sortOrder);
        }
    }

    // Pagination or normal get
    if ($request->has('paginate')) {
        $perPage = $request->get('paginate', 10);
        $lawyers = $query->paginate($perPage);
        $lawyers->getCollection()->transform(fn($lawyer) => $this->formatLawyer($lawyer));
    } else {
        $lawyers = $query->get()->map(fn($lawyer) => $this->formatLawyer($lawyer));
    }

    return response()->json($lawyers);
}

/**
 * Helper function to format each lawyer profile.
 */
private function formatLawyer($lawyer)
{
    return [
        'id' => $lawyer->id,
        'user_id' => $lawyer->user_id,
        'lawyer_name' => $lawyer->user?->name,
        'cover_image' => $lawyer->cover_image,
        'profile_image' => $lawyer->profile_image,
        'amount' => $lawyer->amount,
        'description' => $lawyer->description,
        'education' => $lawyer->education,
        'specialty' => $lawyer->specialty,
        'experience' => $lawyer->experience,
        'verified' => $lawyer->verified,
        'languages' => $lawyer->languages,
        'total_cases' => $lawyer->total_cases,
        'rating' => $lawyer->rating,
        'created_at' => $lawyer->created_at,
        'updated_at' => $lawyer->updated_at,
    ];
}



    public function store(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'cover_image' => 'required|file|image|max:2048',
            'profile_image' => 'required|file|image|max:2048',
            'amount' => 'required|numeric|decimal:0,2',
            'description' => 'required|string',
            'education' => 'required|string',
            'specialty' => 'required|string',
            'experience' => 'required|string',
            'verified' => 'boolean',
            'languages' => 'string',
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
        return $lawyerProfile;
    }

    public function update(Request $request, LawyerProfile $lawyerProfile)
    {
        // Validate input
        $validated = $request->validate([
            'cover_image' => 'nullable|file|image|max:2048',
            'profile_image' => 'nullable|file|image|max:2048',
            'amount' => 'numeric|decimal:0,2',
            'description' => 'string',
            'education' => 'string',
            'specialty' => 'string',
            'experience' => 'string',
            'verified' => 'boolean',
            'languages' => 'string',
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
        $lawyerProfile->delete();
        return response()->json(['message' => 'Profile deleted']);
    }




public function getAuthLawyerProfile()
{
    // Get authenticated user
    $user = Auth::user();

    // Check if user is logged in
    if (!$user) {
        return response()->json(['message' => 'Unauthenticated'], 401);
    }

    // Retrieve lawyer profile by user_id
    $profile = LawyerProfile::where('user_id', $user->id)->first();

    // Check if lawyer profile exists
    if (!$profile) {
        return response()->json(['message' => 'Lawyer profile not found'], 404);
    }

    // Combine user and lawyer profile data
    $data = [
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone ?? null,
            'created_at' => $user->created_at,
        ],
        'lawyer_profile' => $profile,
    ];

    return response()->json([
        'success' => true,
        'data' => $data
    ]);
}

}