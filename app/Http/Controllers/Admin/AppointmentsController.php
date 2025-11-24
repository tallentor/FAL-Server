<?php

namespace App\Http\Controllers\Admin;

use App\Models\Appointment;
use App\Models\PaymentLink;
use Illuminate\Http\Request;
use App\Mail\PaymentLinkMail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;

class AppointmentsController extends Controller
{
    // Get all appointments
    public function getAllAppointments()
{
    // Fetch all appointments with selected fields
    $appointments = Appointment::select([
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
        'created_at',
        'updated_at'
    ])->get();

    return response()->json([
        'success' => true,
        'data' => $appointments
    ]);
}


public function getNotificationsByAppointment($appointment_id)
{
    $user = auth()->user();

    // Only admin can access
    if (!$user || $user->role != 2) {
        return response()->json([
            'status' => false,
            'message' => 'Unauthorized. Only admins can access this data.'
        ], 403);
    }

    // Fetch notifications linked to the specific appointment
    $notifications = \App\Models\AppointmentNotification::where('appointment_id', $appointment_id)
        ->orderBy('created_at', 'desc')
        ->get([
            'id',
            'admin_id',
            'lawyer_id',
            'appointment_id',
            'lawyer_name',
            'client_name',
            'user_phone_number',
            'type',
            'date',
            'time',
            'zoom_link',
            'host_zoom_link',
            'status',
            'created_at',
            'updated_at'
        ]);

    if ($notifications->isEmpty()) {
        return response()->json([
            'status' => false,
            'message' => 'No notifications found for this appointment.',
        ], 404);
    }

    return response()->json([
        'status' => true,
        'message' => 'Notifications retrieved successfully.',
        'data' => $notifications
    ], 200);
}


//Send WhatsApp Message manually
public function SendMassageManually($id)
{
    $user = auth()->user();

    // Check admin role
    if (!$user || $user->role != 2) {
        return response()->json([
            'status' => false,
            'message' => 'Unauthorized. Only admins can update message.'
        ], 403);
    }

    $notification = \App\Models\AppointmentNotification::where('id', $id)
        ->where('admin_id', $user->id)
        ->first();

    if (!$notification) {
        return response()->json([
            'status' => false,
            'message' => 'Notification not found.'
        ], 404);
    }

    // Update status to read
    $notification->status = 'read';
    $notification->save();

    return response()->json([
        'status' => true,
        'message' => 'Send message successfully.',
        'data' => $notification
    ], 200);
}




    // Add payment link to a specific appointment
    public function addPaymentLink(Request $request, $appointment_id)
    {
        $request->validate([
            'payment_link' => 'required|url',
        ]);

        // Find the appointment
        $appointment = Appointment::findOrFail($appointment_id);

        // Create a payment link record
        $payment = PaymentLink::create([
            'appointment_id' => $appointment->id,
            'user_id' => $appointment->user_id,
            'payment_link' => $request->payment_link,
            'status' => 'pending',
        ]);

        // Send email to the appointment's user
        $user = $appointment->user;
        if ($user && $user->email) {
            Mail::to($user->email)->send(new PaymentLinkMail($appointment, $payment));
        }

        return response()->json([
            'success' => true,
            'message' => 'Payment link added and sent to the user successfully.',
            'data' => $payment
        ]);
    }



}
