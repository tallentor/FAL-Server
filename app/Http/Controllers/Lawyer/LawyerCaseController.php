<?php

namespace App\Http\Controllers\Lawyer;

use App\Models\User;
use GuzzleHttp\Client;
use App\Models\CaseModel;
use App\Models\CaseMeeting;
use App\Models\AssignLawyer;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\CaseNotification;
use App\Models\RejectNotification;
use App\Models\ApproveNotification;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use App\Notifications\CaseApprovedNotification;
use App\Notifications\AdminCaseApprovedNotification;
use App\Notifications\AdminCaseRejectedNotification;
use Illuminate\Support\Facades\Http;


class LawyerCaseController extends Controller
{
//Get my assigned cases
public function myAssignedCases()
{
    $lawyer = Auth::user()->lawyerProfile;

    if (!$lawyer) {
        return response()->json(['message' => 'Lawyer profile not found'], 404);
    }

    $cases = $lawyer->assignedCases;

    return response()->json([
        'lawyer' => $lawyer->name,
        'cases' => $cases
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
public function approveCasewadakaranaeka(Request $request, $caseId)
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

    // Meeting datetime
    $meetingDateTime = Carbon::parse("{$request->date} {$request->time}");

    // Create Zoom meeting dynamically
    $zoomLink = $this->createZoomMeeting($meetingDateTime, $lawyer->name);

    // Save meeting info
    $meeting = CaseMeeting::create([
        'case_id' => $caseId,
        'lawyer_id' => $lawyer->id,
        'user_id' => $case->user_id,
        'meeting_date' => $request->date,
        'meeting_time' => $request->time,
        'zoom_link' => 'hgfhgjhgjggjg',
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

    //  Notify the single admin — store notification in table
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
        'message' => 'Case approved successfully',
        'meeting' => $meeting,
    ]);
}





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




public function approveCase(Request $request, $caseId)
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














    // Create Zoom meeting using OAuth token
    // private function createZoomMeeting($startTime, $hostName)
    // {
    //     $client = new Client();
    //     $accessToken = \App\Http\Controllers\ZoomController::getAccessToken();

    //     if (!$accessToken) {
    //         return null;
    //     }

    //     $response = $client->post('https://api.zoom.us/v2/users/me/meetings', [
    //         'headers' => [
    //             'Authorization' => "Bearer $accessToken",
    //             'Content-Type' => 'application/json',
    //         ],
    //         'json' => [
    //             'topic' => "Meeting with $hostName",
    //             'type' => 2,
    //             'start_time' => $startTime->toIso8601String(),
    //             'duration' => 60,
    //             'timezone' => 'Asia/Colombo',
    //             'settings' => [
    //                 'join_before_host' => true,
    //                 'host_video' => true,
    //                 'participant_video' => true,
    //             ],
    //         ],
    //     ]);

    //     $data = json_decode($response->getBody(), true);
    //     return $data['join_url'] ?? null;
    // }




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

public function sendWhatsAppMessageWada($to, $message)
{
    $token = env('WHATSAPP_ACCESS_TOKEN');
    $phoneId = env('WHATSAPP_PHONE_NUMBER_ID');

    $response = Http::withToken($token)->post("https://graph.facebook.com/v19.0/{$phoneId}/messages", [
        'messaging_product' => 'whatsapp',
        'to' => $to,
        'type' => 'text',
        'text' => ['body' => $message],
    ]);

    if ($response->failed()) {
        return response()->json([
            'success' => false,
            'error' => $response->json(),
        ], 400);
    }

    return response()->json([
        'success' => true,
        'message' => 'WhatsApp message sent successfully!',
        'data' => $response->json(),
    ]);
}





public function sendWhatsAppMessage5000(Request $request)
{
    $request->validate([
        'to' => 'required|string',
        'message' => 'required|string',
    ]);

    $to = $request->input('to');
    $message = $request->input('message');

    $token = env('WHATSAPP_ACCESS_TOKEN');
    $phoneId = env('WHATSAPP_PHONE_NUMBER_ID');

    $response = Http::withToken($token)->post("https://graph.facebook.com/v19.0/{$phoneId}/messages", [
        'messaging_product' => 'whatsapp',
        'to' => $to,
        'type' => 'text',
        'text' => ['body' => $message],
    ]);

    if ($response->failed()) {
        return response()->json([
            'success' => false,
            'error' => $response->json(),
        ], 400);
    }

    return response()->json([
        'success' => true,
        'message' => 'WhatsApp message sent successfully!',
        'data' => $response->json(),
    ]);
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