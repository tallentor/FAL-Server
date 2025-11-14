<?php

namespace App\Http\Controllers;

use App\Models\Calendar;
use App\Models\LawyerProfile;
use App\Models\User;
use App\Models\CalendarSlot;
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

        $calendarSlot = CalendarSlot::where('lawyer_profile_id', $validated['lawyer_profile_id'])
            ->latest()
            ->first();

        if ($calendarSlot) {
            $days = $calendarSlot->day;
            $bookedDate = Carbon::parse($validated['date'])->format('Y-m-d');
            $bookedTime = Carbon::parse($validated['time'])->format('H:i');

            // Find and update the specific date's time slots
            foreach ($days as $key => $day) {
                if ($day['date'] === $bookedDate) {
                    // Remove the booked time from the times array
                    $days[$key]['times'] = array_values(
                        array_filter($day['times'], function($time) use ($bookedTime) {
                            return $time !== $bookedTime;
                        })
                    );

                    // If no times left for this date, remove the entire date entry
                    if (empty($days[$key]['times'])) {
                        unset($days[$key]);
                    }
                    break;
                }
            }

            $calendarSlot->day = array_values($days);
            $calendarSlot->save();
        }

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
