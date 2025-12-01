<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;

use App\Models\LawyerProfile;
use App\Models\AppointmentMeeting;
use App\Models\LawyersDeleteAccount;
use Illuminate\Support\Facades\Auth;
use App\Mail\NewLawyerProfileNotification;
use Illuminate\Support\Facades\Mail;

class LawyerProfileController extends Controller
{

public function index1()
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


public function index()
{
    $threshold = Carbon::now()->subMinutes(5); // consider active if last 5 mins

    // Get only approved lawyers with user info
    $lawyers = LawyerProfile::with(['user:id,name,email,last_activity'])
        ->where('status', 'approved')
        ->get();

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




    public function storeWada(Request $request)
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

    // ❗ CHECK FOR DUPLICATE PROFILE
    $existing = LawyerProfile::where('user_id', $validated['user_id'])->first();

    if ($existing) {
        return response()->json([
            'success' => false,
            'message' => 'Profile already exists for this lawyer.',
            'existing_profile_id' => $existing->id
        ], 409); // 409 = Conflict
    }

    // Store files
    if ($request->hasFile('cover_image')) {
        $validated['cover_image'] = $request->file('cover_image')->store('images/Lawyer', 'public');
    }

    if ($request->hasFile('profile_image')) {
        $validated['profile_image'] = $request->file('profile_image')->store('images/Lawyer', 'public');
    }

    if ($request->hasFile('proof_of_authorisation')) {
        $validated['proof_of_authorisation'] = $request->file('proof_of_authorisation')->store('images/Lawyer', 'public');
    }

    if ($request->hasFile('cv')) {
        $validated['cv'] = $request->file('cv')->store('images/Lawyer', 'public');
    }

    if ($request->hasFile('signed_agreement')) {
        $validated['signed_agreement'] = $request->file('signed_agreement')->store('images/Lawyer', 'public');
    }

    // Create the lawyer profile
    $profile = LawyerProfile::create($validated);

    // Send email notifications to all admin users (role = 2)
    $adminUsers = User::where('role', 2)->get();
    $lawyerUser = User::find($validated['user_id']);

    foreach ($adminUsers as $admin) {
        // Skip if email is invalid or empty
        if (empty($admin->email) || !filter_var($admin->email, FILTER_VALIDATE_EMAIL)) {
            \Log::warning("Skipping invalid admin email: " . ($admin->email ?? 'null') . " for admin ID: " . $admin->id);
            continue;
        }

        try {
            Mail::to($admin->email)->send(
                new NewLawyerProfileNotification($admin, $lawyerUser, $validated)
            );
        } catch (\Exception $e) {
            // Log the error but don't stop the process
            \Log::error("Failed to send email to admin {$admin->email}: " . $e->getMessage());
            continue;
        }
    }

    return response()->json([
        'success' => true,
        'message' => 'Lawyer profile created successfully',
        'data' => $profile,
    ], 201);
}


public function update(Request $request, $id)
{
    // Find the existing lawyer profile
    $profile = LawyerProfile::findOrFail($id);

    // Validate input
    $validated = $request->validate([
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

    // Upload files (same as store)
    if ($request->hasFile('cover_image')) {
        $validated['cover_image'] = $request->file('cover_image')->store('images/Lawyer', 'public');
    }

    if ($request->hasFile('profile_image')) {
        $validated['profile_image'] = $request->file('profile_image')->store('images/Lawyer', 'public');
    }

    if ($request->hasFile('proof_of_authorisation')) {
        $validated['proof_of_authorisation'] = $request->file('proof_of_authorisation')->store('images/Lawyer', 'public');
    }

    if ($request->hasFile('cv')) {
        $validated['cv'] = $request->file('cv')->store('images/Lawyer', 'public');
    }

    if ($request->hasFile('signed_agreement')) {
        $validated['signed_agreement'] = $request->file('signed_agreement')->store('images/Lawyer', 'public');
    }

    // Update profile
    $profile->update($validated);

    return response()->json([
        'message' => 'Lawyer profile updated successfully',
        'data' => $profile,
    ]);
}



    public function show(LawyerProfile $lawyerProfile)
    {
        return $lawyerProfile;
    }

    public function update2(Request $request, LawyerProfile $lawyerProfile)
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

public function getLawyerByUser(User $user){

    $lawyer = LawyerProfile::where('user_id' , $user->id)->first();

    return response()->json($lawyer);

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


    //Lawyer's account delete request
    public function deleteLawyerAccount(Request $request)
{
    $user = Auth::user();

    if (!$user) {
        return response()->json(['message' => 'Unauthenticated'], 401);
    }

    // Validate delete reason
    $request->validate([
        'reason' => 'required|string|min:3'
    ]);

    // Only lawyers can delete their account (role = 1)
    if ($user->role != 1) {
        return response()->json(['message' => 'Only lawyers can delete their account'], 403);
    }

    // Lawyer profile
    $profile = LawyerProfile::where('user_id', $user->id)->first();

    // Store delete details BEFORE deleting user
    LawyersDeleteAccount::create([
        'user_id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'phone' => $user->phone ?? null,
        'reason' => $request->reason,
    ]);

    // Delete lawyer profile
    if ($profile) {
        $profile->delete();
    }

    // Delete user account
    $user->delete();

    // Logout from Sanctum
    $request->user()->currentAccessToken()->delete();

    return response()->json([
        'success' => true,
        'message' => 'Lawyer account deleted successfully'
    ], 200);
}


// Get all the pending lawyer profiles for admin
public function getPendingProfiles()
{
    // Fetch lawyer profiles with status 'pending'
    $pendingProfiles = LawyerProfile::with('user')->where('status', 'pending')
        ->get();

    return response()->json([
        'success' => true,
        'data' => $pendingProfiles
    ]);
}

//View specific lawyer profile data by admin
public function viewProfile($id)
{
    $profile = LawyerProfile::with('user')->find($id);

    if (!$profile) {
        return response()->json([
            'success' => false,
            'message' => 'Lawyer profile not found'
        ], 404);
    }

    return response()->json([
        'success' => true,
        'data' => $profile
    ]);
}


//Approve lawyer profile by admin
public function approveLawyerProfile($id)
{
    $profile = LawyerProfile::find($id);

    if (!$profile) {
        return response()->json([
            'success' => false,
            'message' => 'Lawyer profile not found'
        ], 404);
    }

    // Update status
    $profile->status = 'approved';
    $profile->save();

    return response()->json([
        'success' => true,
        'message' => 'Lawyer profile approved successfully',
        'data' => $profile
    ]);
}


//Reject lawyer profile by admin
public function rejectLawyerProfile($id)
{
    $profile = LawyerProfile::find($id);

    if (!$profile) {
        return response()->json([
            'success' => false,
            'message' => 'Lawyer profile not found'
        ], 404);
    }

    // Update status to rejected
    $profile->status = 'rejected';
    $profile->save();

    return response()->json([
        'success' => true,
        'message' => 'Lawyer profile rejected successfully',
        'data' => $profile
    ]);
}

public function getLawyerData(){

    return LawyerProfile::with('user')->get();
    
}


}
