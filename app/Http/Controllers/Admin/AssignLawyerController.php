<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\AssignLawyer;
use Illuminate\Http\Request;
use App\Models\LawyerProfile;
use App\Http\Controllers\Controller;

class AssignLawyerController extends Controller
{
    // Assign lawyer to a case
    public function assignExist(Request $request)
    {
        $request->validate([
            'case_id' => 'required|exists:cases,id',
            'lawyer_id' => 'required|exists:lawyer_profiles,id',
        ]);

        $existing = AssignLawyer::where('case_id', $request->case_id)->first();
        if ($existing) {
            return response()->json(['message' => 'This case already has a lawyer assigned.'], 400);
        }

        $assign = AssignLawyer::create([
            'case_id' => $request->case_id,
            'lawyer_id' => $request->lawyer_id,
        ]);

        // Update lawyer's total_cases
        $lawyer = LawyerProfile::find($request->lawyer_id);
        $lawyer->increment('total_cases');
        $lawyer->increment('new_cases');

        return response()->json([
            'message' => 'Lawyer assigned successfully!',
            'data' => $assign
        ], 201);
    }



    public function assign(Request $request)
{
    $request->validate([
        'case_id' => 'required|exists:cases,id',
        'lawyer_id' => 'required|exists:users,id',
    ]);

    // Check that the user is actually a lawyer (role = 1)
    $lawyer = User::where('id', $request->lawyer_id)
                  ->where('role', 1)
                  ->first();

    if (!$lawyer) {
        return response()->json(['message' => 'Invalid lawyer selected.'], 400);
    }

    // Check if this case already has a lawyer assigned
    $existing = AssignLawyer::where('case_id', $request->case_id)->first();
    if ($existing) {
        return response()->json(['message' => 'This case already has a lawyer assigned.'], 400);
    }

    // Create assign record
    $assign = AssignLawyer::create([
        'case_id' => $request->case_id,
        'lawyer_id' => $request->lawyer_id, // from users table
        'status' => 'pending',
    ]);

    return response()->json([
        'message' => 'Lawyer assigned successfully!',
        'data' => $assign
    ], 201);
}


    public function index()
    {
        $assignments = AssignLawyer::with(['case', 'lawyer'])->get();
        return response()->json($assignments);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'lawyer_id' => 'required|exists:lawyer_profiles,id',
        ]);

        $assign = AssignLawyer::findOrFail($id);
        $assign->update(['lawyer_id' => $request->lawyer_id]);
        return response()->json(['message' => 'Lawyer updated successfully']);
    }

    public function destroy($id)
    {
        $assign = AssignLawyer::findOrFail($id);
        $assign->delete();
        return response()->json(['message' => 'Assignment removed successfully']);
    }
}
