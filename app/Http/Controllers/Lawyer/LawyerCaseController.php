<?php

namespace App\Http\Controllers\Lawyer;


use Log;
use Exception;
use App\Models\User;
use GuzzleHttp\Client;
use App\Models\CaseModel;
use App\Models\CaseMeeting;
use App\Models\AssignLawyer;
use Illuminate\Http\Request;
use App\Mail\CaseApprovedMail;
use Illuminate\Support\Carbon;
use App\Models\CaseNotification;
use App\Models\RejectNotification;
use App\Models\ApproveNotification;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use App\Notifications\CaseApprovedNotification;
use App\Notifications\AdminCaseApprovedNotification;
use App\Notifications\AdminCaseRejectedNotification;


class LawyerCaseController extends Controller
{
//Get my assigned cases
public function myAssignedCases(Request $request)
{
    $lawyer = $request->user();

    // Only if lawyer role
    if (!$lawyer->lawyerProfile) {
    return response()->json(['error' => 'Access denied'], 403);
}

    // Load assigned cases
    $cases = $lawyer->assignedCases()->with('user')->get();

    return response()->json([
        'lawyer' => $lawyer->name,
        'cases' => $cases,
    ]);
}

//Approve a case
// public function approveCase($caseId)
// {
//     $lawyer = Auth::user()->lawyerProfile;

//     if (!$lawyer) {
//         return response()->json(['message' => 'Lawyer profile not found'], 404);
//     }

//     $assign = AssignLawyer::where('lawyer_id', $lawyer->id)
//                           ->where('case_id', $caseId)
//                           ->first();

//     if (!$assign) {
//         return response()->json(['message' => 'Case not found or not assigned to this lawyer'], 404);
//     }

//     $assign->status = 'approved';
//     $assign->save();

//     return response()->json(['message' => 'Case approved successfully']);
// }









//Approve a case with Zoom integration and admin notifications
public function approveCaseDontDoAnything(Request $request, $caseId)
{
    $request->validate([
        'date' => 'required|date',
        'time' => 'required',
    ]);

    // Get authenticated lawyer
    $lawyer = Auth::user();

    if ($lawyer->role != 1) {
        return response()->json(['message' => 'Only lawyers can approve cases'], 403);
    }

    // Find assigned case
    $assign = AssignLawyer::where('lawyer_id', $lawyer->id)
                          ->where('case_id', $caseId)
                          ->first();

    if (!$assign) {
        return response()->json(['message' => 'Case not found or not assigned to this lawyer'], 404);
    }

    // Mark as approved
    $assign->status = 'approved';
    $assign->save();

    // Get the case
    $case = CaseModel::find($caseId);
    if (!$case) {
        return response()->json(['message' => 'Case record not found'], 404);
    }

    // Parse meeting datetime
    $meetingDateTime = Carbon::parse("{$request->date} {$request->time}");

    // Create Zoom meeting using instance
    $zoomController = new \App\Http\Controllers\ZoomController();
    $zoomLink = $zoomController->createZoomMeeting($meetingDateTime, $lawyer->name);

    if (!$zoomLink) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to create Zoom meeting',
        ], 500);
    }

    // Save meeting info
    $meeting = CaseMeeting::create([
        'case_id' => $caseId,
        'lawyer_id' => $lawyer->id,
        'user_id' => $case->user_id,
        'meeting_date' => $request->date,
        'meeting_time' => $request->time,
        'zoom_link' => $zoomLink,
    ]);

    // Notify client by email
    $client = User::find($case->user_id);
    if ($client) {
        $messageBody = "Your case has been approved.\n\n"
                     . "Date: {$meeting->meeting_date}\n"
                     . "Time: {$meeting->meeting_time}\n"
                     . "Zoom Link: {$meeting->zoom_link}";

        Mail::raw($messageBody, function ($msg) use ($client) {
            $msg->to($client->email)->subject('Your Case Meeting Details');
        });
    }

    // Notify single admin — store notification in table
    $admin = User::where('role', 2)->first();
    if ($admin) {
        CaseNotification::create([
            'admin_id' => $admin->id,
            'lawyer_id' => $lawyer->id,
            'case_id' => $caseId,
            'lawyer_name' => $lawyer->name,
            'client_name' => $client ? $client->name : 'Unknown User',
            'type' => 'approved',
            'date' => $request->date,
            'time' => $request->time,
            'zoom_link' => $zoomLink,
            'status' => 'unread',
        ]);
    }

    return response()->json([
        'success' => true,
        'message' => 'Case approved successfully',
        'meeting' => $meeting,
        'zoom_link' => $zoomLink,
    ]);
}




public function approveCasewww(Request $request, $caseId)
{
    $request->validate([
        'date' => 'required|date',
        'time' => 'required',
    ]);

    // Get authenticated lawyer
    $lawyer = Auth::user();
    if ($lawyer->role != 1) {
        return response()->json(['message' => 'Only lawyers can approve cases'], 403);
    }

    // Find assigned case
    $assign = AssignLawyer::where('lawyer_id', $lawyer->id)
                          ->where('case_id', $caseId)
                          ->first();
    if (!$assign) {
        return response()->json(['message' => 'Case not found or not assigned to this lawyer'], 404);
    }

    // Mark as approved
    $assign->status = 'approved';
    $assign->save();

    // Get the case
    $case = CaseModel::find($caseId);
    if (!$case) {
        return response()->json(['message' => 'Case record not found'], 404);
    }

    // Parse meeting datetime
    $meetingDateTime = Carbon::parse("{$request->date} {$request->time}");

    // Create Zoom meeting
    $zoomController = new \App\Http\Controllers\ZoomController();
    $zoomLink = $zoomController->createZoomMeeting($meetingDateTime, $lawyer->name);

    if (!$zoomLink) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to create Zoom meeting',
        ], 500);
    }

    // Save meeting info
    $meeting = CaseMeeting::create([
        'case_id' => $caseId,
        'lawyer_id' => $lawyer->id,
        'user_id' => $case->user_id,
        'meeting_date' => $request->date,
        'meeting_time' => $request->time,
        'zoom_link' => $zoomLink,
    ]);

    // Notify client by email
    $client = User::find($case->user_id);
    if ($client) {
        $messageBody = "Your case has been approved.\n\n"
                     . "Date: {$meeting->meeting_date}\n"
                     . "Time: {$meeting->meeting_time}\n"
                     . "Zoom Link: {$meeting->zoom_link}";

        Mail::raw($messageBody, function ($msg) use ($client) {
            $msg->to($client->email)->subject('Your Case Meeting Details');
        });

        // Send WhatsApp message
        $this->sendWhatsAppMessage([
            'to' => $client->phone_number, // Make sure phone is in international format
            'message' => $messageBody
        ]);
    }

    // Notify single admin — store notification in table
    $admin = User::where('role', 2)->first();
    if ($admin) {
        CaseNotification::create([
            'admin_id' => $admin->id,
            'lawyer_id' => $lawyer->id,
            'case_id' => $caseId,
            'lawyer_name' => $lawyer->name,
            'client_name' => $client ? $client->name : 'Unknown User',
            'type' => 'approved',
            'date' => $request->date,
            'time' => $request->time,
            'zoom_link' => $zoomLink,
            'status' => 'unread',
        ]);
    }

    return response()->json([
        'success' => true,
        'message' => 'Case approved successfully',
        'meeting' => $meeting,
        'zoom_link' => $zoomLink,
    ]);
}




public function approveCase(Request $request, $caseId)
    {
        try {
            $request->validate([
                'date' => 'required|date',
                'time' => 'required',
            ]);

            $lawyer = Auth::user();
            if (!$lawyer || $lawyer->role != 1) {
                return response()->json(['message' => 'Only lawyers can approve cases'], 403);
            }

            $assign = AssignLawyer::where('lawyer_id', $lawyer->id)
                ->where('case_id', $caseId)
                ->first();

            if (!$assign) {
                return response()->json(['message' => 'Case not found or not assigned to this lawyer'], 404);
            }

            $assign->status = 'approved';
            $assign->save();

            $case = CaseModel::find($caseId);
            if (!$case) {
                return response()->json(['message' => 'Case record not found'], 404);
            }

            $meetingDateTime = Carbon::parse("{$request->date} {$request->time}");

            // Create Zoom meeting
            $zoomController = new \App\Http\Controllers\ZoomController();
            $zoomLink = $zoomController->createZoomMeeting($meetingDateTime, $lawyer->name);

            if (!$zoomLink) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create Zoom meeting',
                ], 500);
            }

            //  Save meeting info
            $meeting = CaseMeeting::create([
                'case_id' => $caseId,
                'lawyer_id' => $lawyer->id,
                'user_id' => $case->user_id,
                'meeting_date' => $request->date,
                'meeting_time' => $request->time,
                'zoom_link' => $zoomLink,
            ]);

            //  Notify client by email
            $client = User::find($case->user_id);
            if ($client && $client->email) {
                Mail::to($client->email)->send(new CaseApprovedMail($client, $meeting));
            }

            //  Send WhatsApp message (optional)
            if ($client && $client->phone_number) {
                $messageBody = "Your case has been approved.\n\n"
                    . "Date: {$meeting->meeting_date}\n"
                    . "Time: {$meeting->meeting_time}\n"
                    . "Zoom Link: {$meeting->zoom_link}";

                $this->sendWhatsAppMessage([
                    'to' => $client->phone_number,
                    'message' => $messageBody,
                ]);
            }

            //  Notify admin
            $admin = User::where('role', 2)->first();
            if ($admin) {
                CaseNotification::create([
                    'admin_id' => $admin->id,
                    'lawyer_id' => $lawyer->id,
                    'case_id' => $caseId,
                    'lawyer_name' => $lawyer->name,
                    'client_name' => $client ? $client->name : 'Unknown User',
                    'type' => 'approved',
                    'date' => $request->date,
                    'time' => $request->time,
                    'zoom_link' => $zoomLink,
                    'status' => 'unread',
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Case approved successfully',
                'meeting' => $meeting,
                'zoom_link' => $zoomLink,
            ]);
        } catch (Exception $e) {
            Log::error('Case approval failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }


/**
 * Helper function to send WhatsApp message
 */
private function sendWhatsAppMessage(array $data)
{
    $token = config('whatsapp.access_token');
    $phoneId = config('whatsapp.phone_number_id');

    $response = Http::withToken($token)->post("https://graph.facebook.com/v19.0/{$phoneId}/messages", [
        'messaging_product' => 'whatsapp',
        'to' => $data['to'],
        'type' => 'text',
        'text' => ['body' => $data['message']],
    ]);

    return $response->successful();
}



    public function sendWhatsAppMessage2($to, $message)
{
    $token = env('WHATSAPP_ACCESS_TOKEN');
    $phoneId = env('WHATSAPP_PHONE_NUMBER_ID');

    $response = Http::withToken($token)->post("https://graph.facebook.com/v19.0/{$phoneId}/messages", [
        'messaging_product' => 'whatsapp',
        'to' => $to, // phone number with country code
        'type' => 'text',
        'text' => ['body' => $message],
    ]);

    return $response->json();
}


//Reject Case with custom notification storage
public function rejectCase($caseId)
{
    $lawyer = Auth::user();

    if ($lawyer->role != 1) {
        return response()->json(['message' => 'Only lawyers can reject cases'], 403);
    }

    $assign = AssignLawyer::where('lawyer_id', $lawyer->id)
                          ->where('case_id', $caseId)
                          ->first();

    if (!$assign) {
        return response()->json(['message' => 'Case not found or not assigned to this lawyer'], 404);
    }

    // Update status
    $assign->status = 'rejected';
    $assign->save();

    $case = CaseModel::find($caseId);
    if (!$case) {
        return response()->json(['message' => 'Case record not found'], 404);
    }

    $client = User::find($case->user_id);
    $admin = User::where('role', 2)->first(); // ✅ Only one admin

    if ($admin) {
        CaseNotification::create([
            'admin_id'    => $admin->id,
            'lawyer_id'   => $lawyer->id,
            'case_id'     => $caseId,
            'lawyer_name' => $lawyer->name,
            'client_name' => $client ? $client->name : 'Unknown User',
            'type'        => 'rejected', // ✅ differentiate notification
            'status'      => 'unread',
        ]);
    }

    return response()->json(['message' => 'Case rejected successfully']);
}





}