<?php

namespace App\Http\Controllers;

use App\Models\CalendarSlot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class CalendarSlotController extends Controller
{
    /**
     * Get available slots for a specific lawyer
     */
    public function getSlots($lawyerId)
    {
        try {
            $slots = CalendarSlot::where('lawyer_profile_id', $lawyerId)
                ->latest()
                ->first();

            if (!$slots) {
                return response()->json([
                    'success' => true,
                    'data' => []
                ], 200);
            }

            return response()->json([
                'success' => true,
                'data' => $slots->day
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching slots',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create or update slots for a lawyer
     */
    public function upsertSlots(Request $request)
    {
       $validator = Validator::make($request->all(), [
        'lawyer_profile_id' => 'required|exists:lawyer_profiles,id',
        'slots' => 'required|array',
        'slots.*.date' => 'required|date|after_or_equal:today',
        'slots.*.times' => 'required|array|min:1',
        'slots.*.times.*' => ['required', 'string', 'regex:#^([0-1][0-9]|2[0-3]):[0-5][0-9]$#'],
    ]);


        if ($validator->fails()) {
            \Log::error('Validation failed', $validator->errors()->toArray());
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        try {
            $lawyerProfileId = $request->lawyer_profile_id;
            $slots = $request->slots;

            // Delete old slots for this lawyer
            CalendarSlot::where('lawyer_profile_id', $lawyerProfileId)->delete();

            // Create new slot record (store as array; model casts 'day' => 'array')
            $calendarSlot = CalendarSlot::create([
                'lawyer_profile_id' => $lawyerProfileId,
                'day' => $slots
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Slots updated successfully',
                'data' => $calendarSlot->day
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating slots',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a specific slot
     */
    public function deleteSlot(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lawyer_profile_id' => 'required|exists:lawyer_profiles,id',
            'date' => 'required|date'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $slot = CalendarSlot::where('lawyer_profile_id', $request->lawyer_profile_id)
                ->latest()
                ->first();

            if (!$slot) {
                return response()->json([
                    'success' => false,
                    'message' => 'No slots found'
                ], 404);
            }

            $days = $slot->day;
            $days = array_filter($days, function($day) use ($request) {
                return $day['date'] !== $request->date;
            });

            $slot->day = array_values($days);
            $slot->save();

            return response()->json([
                'success' => true,
                'message' => 'Slot deleted successfully',
                'data' => $slot->day
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting slot',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}