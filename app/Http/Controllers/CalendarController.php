<?php

namespace App\Http\Controllers;

use App\Models\Calendar;
use Illuminate\Http\Request;
use App\Http\Requests\StoreCalendarRequest;
use App\Http\Requests\UpdateCalendarRequest;

class CalendarController extends Controller
{
    public function index()
    {
        return response()->json(Calendar::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'time' => 'required|date_format:H:i:s',
            'case_model_id' => 'required|exists:cases,id',
            'user_id' => 'required|exists:users,id',
            'note' => 'required|string',
        ]);

        $calendar = Calendar::create($validated);

        return response()->json($calendar, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Calendar $calendar)
    {
        return response()->json($calendar);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Calendar $calendar)
    {
        $validated = $request->validate([
            'date' => 'sometimes|required|date',
            'time' => 'sometimes|required|date_format:H:i:s',
            'case_model_id' => 'sometimes|required|exists:cases,id',
            'user_id' => 'sometimes|required|exists:users,id',
            'note' => 'sometimes|required|string',
        ]);

        $calendar->update($validated);

        return response()->json($calendar);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Calendar $calendar)
    {
        $calendar->delete();

        return response()->json(null, 204);
    }
}
