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
    // Get all approved appointments
    public function getApprovedAppointments()
    {
        // Fetch only appointments where status = 'approved'
        $approvedAppointments = Appointment::where('status', 'approved')->get([
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
        ]);

        return response()->json([
            'success' => true,
            'data' => $approvedAppointments
        ]);
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
