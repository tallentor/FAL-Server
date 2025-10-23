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





//Approve a case with meeting scheduling
public function approveCase345(Request $request, $caseId)
{
    $request->validate([
        'date' => 'required|date',
        'time' => 'required',
    ]);

    // Get authenticated lawyer
    $user = Auth::user();

    if ($user->role != 1) {
        return response()->json(['message' => 'Only lawyers can approve cases'], 403);
    }

    // Find the lawyer's assigned case
    $assign = AssignLawyer::where('lawyer_id', $user->id)
                          ->where('case_id', $caseId)
                          ->first();

    if (!$assign) {
        return response()->json(['message' => 'Case not found or not assigned to this lawyer'], 404);
    }

    // Mark as approved
    $assign->status = 'approved';
    $assign->save();

    // Get the case and its user_id
    $case = CaseModel::find($caseId);
    if (!$case) {
        return response()->json(['message' => 'Case record not found'], 404);
    }

    // Get the case owner's user ID
    $userId = $case->user_id;

    $meetingDateTime = Carbon::parse("{$request->date} {$request->time}");

    // Create Zoom meeting
    $zoomLink = $this->createZoomMeeting($meetingDateTime, $user->name);

    // Save meeting info
    $meeting = CaseMeeting::create([
        'case_id' => $caseId,
        'lawyer_id' => $user->id,
        'user_id' => $userId,
        'meeting_date' => $request->date,
        'meeting_time' => $request->time,
        'zoom_link' => 'jhbjjbjhjkhhkhkhkh',
    ]);

    // Notify client
    $client = User::find($userId);
    if ($client) {
        $messageBody = "Your case has been approved.\n\nDate: {$meeting->meeting_date}\nTime: {$meeting->meeting_time}\n🔗 Zoom: {$meeting->zoom_link}";

        Mail::raw($messageBody, function ($msg) use ($client) {
            $msg->to($client->email)->subject('Your Case Meeting Details');
        });
    }

// Notify Admin
    // $admin = User::where('role', 2)->first();
    // if ($admin) {
    //     Notification::send($admin, new AdminCaseApprovedNotification(
    //         $user->name,
    //         $client->name ?? $client->full_name,
    //         $request->date,
    //         $request->time
    //     ));
    // }

    // Notify admin
$admin = User::where('role', 2)->first();
$caseOwnerName = $assign->user ? $assign->user->name : 'Unknown';
if ($admin) {
    $admin->notify(new AdminCaseApprovedNotification(
        $user->name, // lawyer
        $caseOwnerName, // case owner
        $request->date,
        $request->time
    ));
}

    return response()->json([
        'message' => 'Case approved successfully',
        'meeting' => $meeting,
    ]);
}


//Approve a case with Zoom integration and admin notifications
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






    // Create Zoom meeting using OAuth token
    private function createZoomMeeting($startTime, $hostName)
    {
        $client = new Client();
        $accessToken = \App\Http\Controllers\ZoomController::getAccessToken();

        if (!$accessToken) {
            return null;
        }

        $response = $client->post('https://api.zoom.us/v2/users/me/meetings', [
            'headers' => [
                'Authorization' => "Bearer $accessToken",
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'topic' => "Meeting with $hostName",
                'type' => 2,
                'start_time' => $startTime->toIso8601String(),
                'duration' => 60,
                'timezone' => 'Asia/Colombo',
                'settings' => [
                    'join_before_host' => true,
                    'host_video' => true,
                    'participant_video' => true,
                ],
            ],
        ]);

        $data = json_decode($response->getBody(), true);
        return $data['join_url'] ?? null;
    }

    // Send WhatsApp using Twilio
    // private function sendWhatsAppMessage($to, $message)
    // {
    //     $sid = env('TWILIO_SID');
    //     $token = env('TWILIO_AUTH_TOKEN');
    //     $from = "whatsapp:" . env('TWILIO_WHATSAPP_FROM');

    //     $twilio = new TwilioClient($sid, $token);

    //     $twilio->messages->create("whatsapp:$to", [
    //         'from' => $from,
    //         'body' => $message,
    //     ]);
    // }




//Reject Case with custom notification storage
public function rejectCasewada($caseId)
{
    $lawyer = Auth::user();

    if ($lawyer->role != 1) {
        return response()->json(['message' => 'Only lawyers can reject cases'], 403);
    }

    // Find the case assignment
    $assign = AssignLawyer::where('lawyer_id', $lawyer->id)
                          ->where('case_id', $caseId)
                          ->first();

    if (!$assign) {
        return response()->json(['message' => 'Case not found or not assigned to this lawyer'], 404);
    }

    // Update case status
    $assign->status = 'rejected';
    $assign->save();

    // Get client and admins
    $client = User::find($assign->user_id);
    $admins = User::where('role', 2)->get();

    // Store notification in custom reject_notifications table
    foreach ($admins as $admin) {
        RejectNotification::create([
            'admin_id' => $admin->id,
            'lawyer_id' => $lawyer->id,
            'case_id' => $caseId,
            'lawyer_name' => $lawyer->name,
            'client_name' => $client ? $client->name : 'Unknown User',
            'status' => 'unread',
        ]);
    }

    return response()->json(['message' => 'Case rejected successfully']);
}


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
