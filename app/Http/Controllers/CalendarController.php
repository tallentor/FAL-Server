<?php

namespace App\Http\Controllers;

use App\Models\Calendar;
use App\Models\LawyerProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CalendarController extends Controller
{
    /**
     * Display a list of calendars.
     */
    public function index()
    {
        $calendars = Calendar::with('user', 'lawyerProfile')->get();
        return response()->json($calendars);
    }

    /**
     * Store a new calendar appointment.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'lawyer_profile_id' => 'required|exists:lawyer_profiles,id',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i:s',
            'note' => 'nullable|string',
        ]);

        $calendar = Calendar::create($validated);

        return response()->json($calendar->load('user', 'lawyerProfile'), 201);
    }

    /**
     * Display a specific calendar.
     */
    public function show(Calendar $calendar)
    {
        return response()->json($calendar->load('user', 'lawyerProfile'));
    }

    /**
     * Update a calendar appointment.
     */
    public function update(Request $request, Calendar $calendar)
    {
        $validated = $request->validate([
            'date' => 'sometimes|date',
            'time' => 'sometimes|date_format:H:i:s',
            'note' => 'nullable|string',
        ]);

        $calendar->update($validated);
        return response()->json($calendar->load('user', 'lawyerProfile'));
    }

    /**
     * Delete a calendar appointment.
     */
    public function destroy(Calendar $calendar)
    {
        $calendar->delete();
        return response()->json(null, 204);
    }

   public function filterLawyers(User $user)
{
    $lawyer = LawyerProfile::where('user_id', $user->id)->first();

    if (!$lawyer) {
        return response()->json(['message' => 'Lawyer not found'], 404);
    }

    $calendars = Calendar::where('lawyer_profile_id', $lawyer->id)->with('user', 'lawyerProfile')->get();
    return response()->json($calendars);
}
}
