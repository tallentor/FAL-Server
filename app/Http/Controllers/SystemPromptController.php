<?php

namespace App\Http\Controllers;

use App\Models\SystemPrompt;
use Illuminate\Http\Request;

class SystemPromptController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(SystemPrompt::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'prompt_text' => 'required|string',
            'is_active' => 'boolean'
        ]);

        $systemPrompt = SystemPrompt::create($validated);
        return response()->json($systemPrompt, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(SystemPrompt $systemPrompt)
    {
        return response()->json($systemPrompt);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SystemPrompt $systemPrompt)
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'prompt_text' => 'sometimes|required|string',
            'is_active' => 'sometimes|boolean'
        ]);

        $systemPrompt->update($validated);
        return response()->json($systemPrompt);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SystemPrompt $systemPrompt)
    {
        $systemPrompt->delete();
        return response()->json(null, 204);
    }
}
