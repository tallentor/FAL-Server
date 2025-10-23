<?php

namespace App\Http\Controllers\User;

use App\Models\CaseModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CaseController extends Controller
{
    public function storeExist(Request $request)
    {
        $request->validate([
            'case_title' => 'required|string|max:255',
            'case_description' => 'required|string',
            'reason_for_immigration' => 'required|string|max:255',
            'type_of_visa' => 'nullable|string|max:255',
            'country_of_destination' => 'nullable|string|max:255',
            'visa_expiry_date' => 'nullable|date',
            'immigration_history' => 'nullable|string',
        ]);

        $case = CaseModel::create([
            'user_id' => Auth::id(),
            'type_of_visa' => $request->type_of_visa,
            'country_of_destination' => $request->country_of_destination,
            'visa_expiry_date' => $request->visa_expiry_date,
            'immigration_history' => $request->immigration_history,
            'case_title' => $request->case_title,
            'case_description' => $request->case_description,
            'reason_for_immigration' => $request->reason_for_immigration,
        ]);

        return response()->json([
            'message' => 'Case created successfully',
            'case' => $case,
        ], 201);
    }


    public function store(Request $request)
{
    $request->validate([
        'full_name' => 'required|string|max:255',
        'case_title' => 'required|string|max:255',
        'case_description' => 'required|string',
        'type_of_visa' => 'nullable|string|max:255',
        'country_of_destination' => 'nullable|string|max:255',
        'current_visa_status' => 'nullable|string|max:255',
        'visa_expiry_date' => 'nullable|date',
        'immigration_history' => 'nullable|string',
        'reason_for_immigration' => 'required|string|max:255',
        'previous_visa_denials' => 'nullable|string',
        'number_of_dependents' => 'nullable|integer',
        'additional_notes' => 'nullable|string',
    ]);

    $case = CaseModel::create([
        'user_id' => Auth::id(),
        'full_name' => $request->full_name,
        'case_title' => $request->case_title,
        'case_description' => $request->case_description,
        'type_of_visa' => $request->type_of_visa,
        'country_of_destination' => $request->country_of_destination,
        'current_visa_status' => $request->current_visa_status,
        'visa_expiry_date' => $request->visa_expiry_date,
        'immigration_history' => $request->immigration_history,
        'reason_for_immigration' => $request->reason_for_immigration,
        'previous_visa_denials' => $request->previous_visa_denials,
        'number_of_dependents' => $request->number_of_dependents,
        'additional_notes' => $request->additional_notes,
    ]);

    return response()->json([
        'message' => 'Case created successfully',
        'case' => $case,
    ], 201);
}


    public function index1()
    {
        $cases = CaseModel::where('user_id', Auth::id())->get();
        $cases = CaseModel::all();
        return response()->json([
            'cases' => $cases,
        ], 200);
    }


    public function index()
{
    $user = Auth::user();
    if (!$user) {
        return response()->json([
            'message' => 'Unauthenticated.'
        ], 401);
    }

    $cases = CaseModel::where('user_id', $user->id)->get();

    return response()->json([
        'cases' => $cases,
    ], 200);
}
}