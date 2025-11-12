<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\LawyerProfile;
use App\Models\AppointmentMeeting;
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


    public function index2(Request $request)
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





public function index()
{
    $threshold = Carbon::now()->subMinutes(5); // consider active if last 5 mins

    // Get all lawyers with user info
    $lawyers = LawyerProfile::with(['user:id,name,email,last_activity'])->get();

    // Transform data
    $data = $lawyers->transform(function ($lawyer) use ($threshold) {
        $isActive = optional($lawyer->user)->last_activity
            ? Carbon::parse($lawyer->user->last_activity)->gte($threshold)
            : false;

        return [
            'id' => $lawyer->id,
            'user_id' => $lawyer->user_id,
            'lawyer_name' => $lawyer->lawyer_name ?? $lawyer->user->name ?? null,
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
            'user_email' => $lawyer->user->email ?? null,
            'last_activity' => $lawyer->user->last_activity ?? null,
            'is_active' => $isActive,
        ];
    });

    // Return response
    return response()->json([
        'success' => true,
        'lawyers' => $data,
    ]);
}




//========================================================

public function getAllLawyers(Request $request)
{
    $threshold = Carbon::now()->subMinutes(5);

    // Load lawyer profiles + related user data
    $query = LawyerProfile::with(['user:id,name,email,last_activity']);

    // ----- Filtering -----
    if ($request->has('min_cases')) {
        $query->where('total_cases', '>=', $request->min_cases);
    }

    if ($request->has('max_cases')) {
        $query->where('total_cases', '<=', $request->max_cases);
    }

    if ($request->has('min_rating')) {
        $query->where('rating', '>=', $request->min_rating);
    }

    if ($request->has('max_rating')) {
        $query->where('rating', '<=', $request->max_rating);
    }

    if ($request->has('verified')) {
        $verified = filter_var($request->verified, FILTER_VALIDATE_BOOLEAN);
        $query->where('verified', $verified);
    }

    // ----- Sorting -----
    if ($request->has('sort_by')) {
        $sortField = $request->get('sort_by');
        $sortOrder = $request->get('sort_order', 'desc');

        if (in_array($sortField, ['total_cases', 'rating'])) {
            $query->orderBy($sortField, $sortOrder);
        }
    }

    // ----- Pagination or Full Data -----
    if ($request->has('paginate')) {
        $perPage = $request->get('paginate', 10);
        $lawyers = $query->paginate($perPage);
    } else {
        $lawyers = $query->get();
    }

    // ----- Add is_active and user info -----
    $lawyers->transform(function ($lawyer) use ($threshold) {
        $isActive = false;

        if ($lawyer->user && $lawyer->user->last_activity) {
            $isActive = $lawyer->user->last_activity >= $threshold;
        }

        return [
            'id' => $lawyer->id,
            'user_id' => $lawyer->user_id,
            'lawyer_name' => $lawyer->lawyer_name ?? $lawyer->user->name ?? null,
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
            'user_email' => $lawyer->user->email ?? null,
            'last_activity' => $lawyer->user->last_activity ?? null,
            'is_active' => $isActive,
        ];
    });

    // Optional: filter only active lawyers if requested
    if ($request->boolean('only_active')) {
        $lawyers = $lawyers->filter(fn($l) => $l['is_active'])->values();
    }

    return response()->json([
        'success' => true,
        'lawyers' => $lawyers,
    ]);
}
//========================================================

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



    public function store1(Request $request)
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


    public function store(Request $request)
{
    // Validate input
    $validated = $request->validate([
        'user_id' => 'required|exists:users,id',
        'cover_image' => 'nullable|file|image|max:2048',
        'profile_image' => 'nullable|file|image|max:2048',
        'amount' => 'nullable|numeric|decimal:0,2',
        'description' => 'nullable|string',
        'education' => 'nullable|string',
        'specialty' => 'nullable|string',
        'experience' => 'nullable|string',
        'verified' => 'nullable|boolean',
        'languages' => 'nullable|string',

        // New fields
        'id_or_passport' => 'nullable|string|max:255',
        'proof_of_authorisation' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:4096',
        'bar_association_id' => 'nullable|string|max:255',
        'cv' => 'nullable|file|mimes:pdf,doc,docx|max:4096',
        'signed_agreement' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:4096',
        'areas_of_practice' => 'nullable|string',
    ]);

    // Store files in public/images/Lawyer
    if ($request->hasFile('cover_image')) {
        $coverImagePath = $request->file('cover_image')->store('images/Lawyer', 'public');
        $validated['cover_image'] = $coverImagePath;
    }

    if ($request->hasFile('profile_image')) {
        $profileImagePath = $request->file('profile_image')->store('images/Lawyer', 'public');
        $validated['profile_image'] = $profileImagePath;
    }

    if ($request->hasFile('proof_of_authorisation')) {
        $proofPath = $request->file('proof_of_authorisation')->store('images/Lawyer', 'public');
        $validated['proof_of_authorisation'] = $proofPath;
    }

    if ($request->hasFile('cv')) {
        $cvPath = $request->file('cv')->store('images/Lawyer', 'public');
        $validated['cv'] = $cvPath;
    }

    if ($request->hasFile('signed_agreement')) {
        $agreementPath = $request->file('signed_agreement')->store('images/Lawyer', 'public');
        $validated['signed_agreement'] = $agreementPath;
    }

    // Create the lawyer profile
    $profile = LawyerProfile::create($validated);

    return response()->json([
        'message' => 'Lawyer profile created successfully',
        'data' => $profile,
    ], 201);
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


//Get lawyer zoom link
public function getZoomLink(Request $request, $appointment_id)
    {
        $lawyer = $request->user();

        // Ensure only lawyer can access this route
        if ($lawyer->role != 1) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Fetch the specific appointment meeting
        $meeting = AppointmentMeeting::where('appointment_id', $appointment_id)
            ->where('lawyer_id', $lawyer->id)
            ->first();

        if (!$meeting) {
            return response()->json(['message' => 'Meeting not found'], 404);
        }

        return response()->json([
            'appointment_id' => $meeting->appointment_id,
            'user_id' => $meeting->user_id,
            'zoom_link' => $meeting->zoom_link,
            'host_link' => $meeting->host_link,
        ]);
    }

}