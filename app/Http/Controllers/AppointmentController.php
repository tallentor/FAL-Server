<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Appointment;

use Illuminate\Http\Request;
use App\Models\LawyerProfile;
use App\Mail\CaseApprovedMail;
use App\Models\AppointmentMeeting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Mail\AppointmentApprovedMail;
use App\Models\AppointmentNotification;
use App\Mail\LawyerAppointmentNotification;

class AppointmentController extends Controller
{

    //Appointment Store
    public function storeAppointment1(Request $request)
{
    $request->validate([
        'lawyer_id' => 'required|exists:users,id',
        'appointment_date' => 'required|date|after_or_equal:today',
        'appointment_time' => 'required|date_format:H:i',
        'full_name' => 'required|string|max:255',
        'case_title' => 'required|string|max:255',
        'case_description' => 'required|string',
        'type_of_visa' => 'nullable|string|max:255',
        'country_of_destination' => 'nullable|string|max:255',
        'current_visa_status' => 'nullable|string|max:255',
        'visa_expiry_date' => 'nullable|date',
        'immigration_history' => 'nullable|string',
        'reason_for_immigration' => 'nullable|string|max:255',
        'previous_visa_denials' => 'nullable|string',
        'number_of_dependents' => 'nullable|integer',
        'additional_notes' => 'nullable|string',
    ]);

    // Check if this lawyer has a lawyer profile
    $lawyerProfile = LawyerProfile::where('user_id', $request->lawyer_id)->first();

    if (!$lawyerProfile) {
        return response()->json([
            'message' => 'Selected user is not a registered lawyer (no lawyer profile found).'
        ], 422);
    }

    // Get consultation fee from lawyer's profile
    $consultationFee = $lawyerProfile->amount;

    if (!$consultationFee || $consultationFee <= 0) {
        return response()->json([
            'message' => 'This lawyer has not set their consultation fee yet.'
        ], 422);
    }

    // Create the appointment
    $appointment = Appointment::create([
        'user_id' => Auth::id(),
        'lawyer_id' => $request->lawyer_id,
        'appointment_date' => $request->appointment_date,
        'appointment_time' => $request->appointment_time,
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
        'payment_status' => 'pending_payment', // Appointment pending until payment
    ]);

    // Load related lawyer + lawyer profile for response
    $appointment->load(['lawyer', 'lawyerProfile']);

    return response()->json([
        'message' => 'Appointment created successfully. Please proceed with payment.',
        'appointment' => $appointment,
        'payment_required' => true,
        'consultation_fee' => $consultationFee,
        'lawyer_name' => $appointment->lawyer->name,
        'currency' => 'usd', // or get from config/lawyer profile
    ], 201);
}


public function storeAppointment(Request $request)
{
    $request->validate([
        'lawyer_id' => 'required|exists:users,id',
        'appointment_date' => 'required|date|after_or_equal:today',
        'appointment_time' => 'required|date_format:H:i',
        'full_name' => 'required|string|max:255',
        'case_title' => 'required|string|max:255',
        'case_description' => 'required|string',
        'type_of_visa' => 'nullable|string|max:255',
        'country_of_destination' => 'nullable|string|max:255',
        'current_visa_status' => 'nullable|string|max:255',
        'visa_expiry_date' => 'nullable|date',
        'immigration_history' => 'nullable|string',
        'reason_for_immigration' => 'nullable|string|max:255',
        'previous_visa_denials' => 'nullable|string',
        'number_of_dependents' => 'nullable|integer',
        'additional_notes' => 'nullable|string',
    ]);

    // Check lawyer profile
    $lawyerProfile = LawyerProfile::where('user_id', $request->lawyer_id)->first();

    if (!$lawyerProfile) {
        return response()->json([
            'message' => 'Selected user is not a registered lawyer (no lawyer profile found).'
        ], 422);
    }

    $consultationFee = $lawyerProfile->amount;

    if (!$consultationFee || $consultationFee <= 0) {
        return response()->json([
            'message' => 'This lawyer has not set their consultation fee yet.'
        ], 422);
    }

    // Create appointment
    $appointment = Appointment::create([
        'user_id' => Auth::id(),
        'lawyer_id' => $request->lawyer_id,
        'appointment_date' => $request->appointment_date,
        'appointment_time' => $request->appointment_time,
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
        'payment_status' => 'pending_payment',
    ]);

    $appointment->load(['lawyer', 'lawyerProfile']);

    // Send email to lawyer
    Mail::to($appointment->lawyer->email)
        ->send(new LawyerAppointmentNotification($appointment));

    return response()->json([
        'message' => 'Appointment created successfully. Lawyer has been notified via email. Please proceed with payment.',
        'appointment' => $appointment,
        'payment_required' => true,
        'consultation_fee' => $consultationFee,
        'lawyer_name' => $appointment->lawyer->name,
        'currency' => 'usd',
    ], 201);
}


    // Get all appointments for a specific lawyer.
    public function getMyAppointments()
    {
        $lawyer = Auth::user();

        // Optional: check if the user is a lawyer (if your system has roles)
        if ($lawyer->role !== 1) {
            return response()->json([
                'error' => 'Access denied. Only lawyers can view their appointments.'
            ], 403);
        }

        // Fetch all appointments where lawyer_id = current user's id
        $appointments = Appointment::where('lawyer_id', $lawyer->id)
            ->with('user') // Include client/user info
            ->orderBy('appointment_date', 'asc')
            ->get();

        if ($appointments->isEmpty()) {
            return response()->json([
                'message' => 'No appointments found for this lawyer.',
            ], 404);
        }

        return response()->json([
            'message' => 'Appointments retrieved successfully.',
            'appointments' => $appointments,
        ], 200);
    }




    // Appointment approve
    public function approveAppointmentExist(Request $request, $appointmentId)
    {
    try {
        $lawyer = Auth::user();

        if (!$lawyer || $lawyer->role != 1) {
            return response()->json(['message' => 'Only lawyers can approve appointments'], 403);
        }

        // Get appointment
        $appointment = Appointment::find($appointmentId);
        if (!$appointment) {
            return response()->json(['message' => 'Appointment not found'], 404);
        }

        // Mark as approved
        $appointment->status = 'approved';
        $appointment->save();

        // Appointment date/time
        $meetingDateTime = Carbon::parse("{$appointment->appointment_date} {$appointment->appointment_time}");

        // Create Zoom meeting
        $zoomController = new \App\Http\Controllers\ZoomController();
        $zoomLinks = $zoomController->createZoomMeeting($meetingDateTime, $lawyer->name);

        if (!$zoomLinks || !isset($zoomLinks['join_url'])) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create Zoom meeting'
            ], 500);
        }

        $joinUrl = $zoomLinks['join_url'];
        $hostUrl = $zoomLinks['start_url'] ?? null;

        // Save meeting details
        $meeting = AppointmentMeeting::create([
            'appointment_id' => $appointmentId,
            'lawyer_id' => $lawyer->id,
            'user_id' => $appointment->user_id,
            'zoom_link' => $joinUrl,
            'host_link' => $hostUrl,
        ]);

        // Send email to client
        $client = User::find($appointment->user_id);
        if ($client && $client->email) {
            Mail::to($client->email)->send(new CaseApprovedMail($client, $meeting));
        }

        // Send WhatsApp message
        if ($client && $client->phone_number) {
            $messageBody = "Your appointment has been approved.\n\n"
                . "Date: {$appointment->appointment_date}\n"
                . "Time: {$appointment->appointment_time}\n"
                . "Zoom Link: {$joinUrl}";

            $this->sendWhatsAppMessage([
                'to' => $client->phone_number,
                'message' => $messageBody,
            ]);
        }

        // Notify admin
        $admin = User::where('role', 2)->first();
        if ($admin) {
            AppointmentNotification::create([
                'admin_id' => $admin->id,
                'lawyer_id' => $lawyer->id,
                'appointment_id' => $appointmentId,
                'lawyer_name' => $lawyer->name,
                'client_name' => $client ? $client->name : 'Unknown User',
                'type' => 'approved',
                'date' => $appointment->appointment_date,
                'time' => $appointment->appointment_time,
                'zoom_link' => $joinUrl,
                'status' => 'unread',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Appointment approved successfully',
            'meeting' => $meeting,
            'zoom_links' => [
                'join_url' => $joinUrl,
                'host_url' => $hostUrl,
            ],
        ]);
    } catch (Exception $e) {
        Log::error('Appointment approval failed: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Something went wrong',
            'error' => $e->getMessage()
        ], 500);
    }
}


public function approveAppointment(Request $request, $appointmentId)
{
    try {

        $lawyer = Auth::user();

        if (!$lawyer || $lawyer->role != 1) {
            return response()->json(['message' => 'Only lawyers can approve appointments'], 403);
        }

        // Get appointment
        $appointment = Appointment::find($appointmentId);
        if (!$appointment) {
            return response()->json(['message' => 'Appointment not found'], 404);
        }

        // Mark as approved
        $appointment->status = 'approved';
        $appointment->save();

        // Appointment date/time
        $meetingDateTime = Carbon::parse("{$appointment->appointment_date} {$appointment->appointment_time}");

        // Create Zoom meeting
        $zoomController = new \App\Http\Controllers\ZoomController();
        $zoomLinks = $zoomController->createZoomMeeting($meetingDateTime, $lawyer->name);

        if (!$zoomLinks || !isset($zoomLinks['join_url'])) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create Zoom meeting'
            ], 500);
        }

        $joinUrl = $zoomLinks['join_url'];
        $hostUrl = $zoomLinks['start_url'] ?? null;

        // Save meeting details
        $meeting = AppointmentMeeting::create([
            'appointment_id' => $appointmentId,
            'lawyer_id'      => $lawyer->id,
            'user_id'        => $appointment->user_id,
            'zoom_link'      => $joinUrl,
            'host_link'      => $hostUrl,
        ]);

        // Get client
        $client = User::find($appointment->user_id);

        // Send email to client
        if ($client && $client->email) {
            Mail::to($client->email)->send(new CaseApprovedMail($client, $meeting));
        }

        // Send WhatsApp message
        if ($client && $client->phone_number) {
            $messageBody = "Your appointment has been approved.\n\n"
                . "Date: {$appointment->appointment_date}\n"
                . "Time: {$appointment->appointment_time}\n"
                . "Zoom Link: {$joinUrl}";

            $this->sendWhatsAppMessage([
                'to'      => $client->phone_number,
                'message' => $messageBody,
            ]);
        }

        // Notify admin
        $admin = User::where('role', 2)->first();

        if ($admin) {
            AppointmentNotification::create([
                'admin_id'          => $admin->id,
                'lawyer_id'         => $lawyer->id,
                'appointment_id'    => $appointmentId,
                'lawyer_name'       => $lawyer->name,
                'client_name'       => $client ? $client->name : 'Unknown User',
                'user_phone_number' => $client ? $client->phone_number : null,
                'type'              => 'approved',
                'date'              => $appointment->appointment_date,
                'time'              => $appointment->appointment_time,
                'zoom_link'         => $joinUrl,
                'host_zoom_link'    => $hostUrl,
                'status'            => 'unread',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Appointment approved successfully',
            'meeting' => $meeting,
            'zoom_links' => [
                'join_url' => $joinUrl,
                'host_url' => $hostUrl,
            ],
        ]);

    } catch (Exception $e) {

        Log::error('Appointment approval failed: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Something went wrong',
            'error'   => $e->getMessage()
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



public function getUserAppointments()
{
    $user = Auth::user();

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthenticated'
        ], 401);
    }

    // Get appointments for the logged-in user
    $appointments = Appointment::where('user_id', $user->id)
        ->orderBy('appointment_date', 'asc')
        ->get([
            'id',
            'user_id',
            'lawyer_id',
            'appointment_date',
            'appointment_time',
            'full_name',
            'case_title',
            'case_description',
            'type_of_visa',
            'country_of_destination',
            'current_visa_status',
            'visa_expiry_date',
            'immigration_history',
            'reason_for_immigration',
            'previous_visa_denials',
            'number_of_dependents',
            'additional_notes',
            'status',
            'payment_status',
            'created_at',
            'updated_at'
        ]);

    return response()->json([
        'success' => true,
        'data' => $appointments
    ]);
}

public function getSumAppointmnets(User $user){

    return Appointment::where('lawyer_id', $user->id)->count();

}

public function getfilterbyToday(User $user){

    return Appointment::where('lawyer_id', $user->id)
        ->whereDate('appointment_date', Carbon::today())
        ->count();
}


public function getApprovedAppointments(User $user)
{
    return Appointment::where('lawyer_id', $user->id)
        ->where('status', 'approved')
        ->get();
}

public function getPendingAppointments(User $user)
{
    return Appointment::where('lawyer_id', $user->id)
        ->where('status', 'pending')
        ->get();
}




}