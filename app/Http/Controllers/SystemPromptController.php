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

        $system_prompt = SystemPrompt::create($validated);
        return response()->json($system_prompt, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(SystemPrompt $system_prompt)
    {
        return response()->json($system_prompt);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SystemPrompt $system_prompt)
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'prompt_text' => 'sometimes|required|string',
            'is_active' => 'sometimes|boolean'
        ]);

        $system_prompt->update($validated);
        return response()->json($system_prompt);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SystemPrompt $system_prompt)
    {
        $system_prompt->delete();
        return response()->json(null, 204);
    }
}
