<?php

namespace App\Http\Controllers\Lawyer;

use App\Models\User;
use GuzzleHttp\Client;
use App\Models\CaseMeeting;
use App\Models\AssignLawyer;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

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



public function approveCase(Request $request, $caseId)
    {
        $request->validate([
            'date' => 'required|date',
            'time' => 'required',
        ]);

        $lawyer = Auth::user()->lawyerProfile;

        if (!$lawyer) {
            return response()->json(['message' => 'Lawyer profile not found'], 404);
        }

        $assign = AssignLawyer::where('lawyer_id', $lawyer->id)
                              ->where('case_id', $caseId)
                              ->first();

        if (!$assign) {
            return response()->json(['message' => 'Case not found or not assigned to this lawyer'], 404);
        }

        $assign->status = 'approved';
        $assign->save();

        $meetingDateTime = Carbon::parse("{$request->date} {$request->time}");

        // Create Zoom meeting
        $zoomLink = $this->createZoomMeeting($meetingDateTime, $lawyer->user->name);

        // Save meeting info
        $meeting = CaseMeeting::create([
            'case_id' => $caseId,
            'lawyer_id' => $lawyer->id,
            'user_id' => $assign->user_id,
            'meeting_date' => $request->date,
            'meeting_time' => $request->time,
            'zoom_link' => $zoomLink,
        ]);

        // Notify user
        $user = User::find($assign->user_id);

        if ($user) {
            $messageBody = "Your case has been approved.\n\n Date: {$meeting->meeting_date}\n Time: {$meeting->meeting_time}\n🔗 Zoom: {$meeting->zoom_link}";

            // Send Email
            Mail::raw($messageBody, function ($msg) use ($user) {
                $msg->to($user->email)->subject('Your Case Meeting Details');
            });

            // Send WhatsApp
            // $this->sendWhatsAppMessage($user->phone, $messageBody);
        }

        return response()->json([
            'message' => 'Case approved successfully',
            'meeting' => $meeting,
        ]);
    }

    // 🔹 Create Zoom meeting using OAuth token
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



















//Reject Case
public function rejectCase($caseId)
{
    $lawyer = Auth::user()->lawyerProfile;

    if (!$lawyer) {
        return response()->json(['message' => 'Lawyer profile not found'], 404);
    }

    $assign = AssignLawyer::where('lawyer_id', $lawyer->id)
                          ->where('case_id', $caseId)
                          ->first();

    if (!$assign) {
        return response()->json(['message' => 'Case not found or not assigned to this lawyer'], 404);
    }

    $assign->status = 'rejected';
    $assign->save();

    return response()->json(['message' => 'Case rejected successfully']);
}







}
