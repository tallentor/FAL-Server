<?php

namespace App\Http\Controllers;

use App\Models\CalendarSlot;
use Illuminate\Http\Request;

class CalendarSlotController extends Controller
{
    /**
     * Display all slots (optional: include calendar info).
     */
    public function index()
    {
        $slots = CalendarSlot::with('calendar')->get();
        return response()->json($slots);
    }

    /**
     * Create a new slot record manually.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'calendar_id' => 'required|exists:calendars,id',
            'day'         => 'required|date',
            'slot_1'      => 'nullable|array',
            'slot_2'      => 'nullable|array',
            'slot_3'      => 'nullable|array',
        ]);

        $slot = CalendarSlot::create($validated);

        return response()->json($slot, 201);
    }

    /**
     * Display one slot (with its calendar).
     */
    public function show(CalendarSlot $calendarSlot)
    {
        return response()->json($calendarSlot->load('calendar'));
    }

    /**
     * Update JSON-based slots.
     */
    public function update(Request $request, CalendarSlot $calendarSlot)
    {
        $validated = $request->validate([
            'slot_1' => 'nullable|array',
            'slot_2' => 'nullable|array',
            'slot_3' => 'nullable|array',
        ]);

        $calendarSlot->update($validated);

        return response()->json([
            'message' => 'Slot updated successfully',
            'data'    => $calendarSlot
        ]);
    }

    /**
     * Delete a slot (one day’s schedule).
     */
    public function destroy(CalendarSlot $calendarSlot)
    {
        $calendarSlot->delete();

        return response()->json(['message' => 'Slot deleted successfully'], 204);
    }
}
