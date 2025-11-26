<?php

namespace App\Http\Controllers;

use Exception;
use Stripe\Stripe;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Stripe\Checkout\Session;
use App\Models\LawyerProfile;
use App\Models\StripePayment;
use App\Mail\PaymentFailedMail;
use App\Mail\PaymentSuccessMail;
use App\Models\LawyersDeleteAccount;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\AdminPaymentNotificationMail;
use App\Mail\LawyerAppointmentNotification;

class StripePaymentController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Create Stripe Checkout Session
     */
    public function createCheckoutSession(Request $request, $appointmentId)
    {
        try {
            $request->validate([
                'amount' => 'required|numeric|min:1',
            ]);

            $appointment = Appointment::findOrFail($appointmentId);

            if ($appointment->user_id !== Auth::id()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            // Prevent double payment
            $existing = StripePayment::where('appointment_id', $appointmentId)
                ->where('status', 'succeeded')
                ->first();

            if ($existing) {
                return response()->json(['message' => 'Payment already completed'], 422);
            }

            // Create Stripe Checkout session
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => $request->currency ?? 'usd',
                        'product_data' => [
                            'name' => "Appointment Payment - {$appointment->case_title}",
                        ],
                        'unit_amount' => $request->amount * 100,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => env('FRONTEND_URL') . '/payment-success?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url'  => env('FRONTEND_URL') . '/payment-cancel',
            ]);

            // Store session ID
            StripePayment::create([
                'appointment_id' => $appointmentId,
                'user_id' => Auth::id(),
                'lawyer_id' => $appointment->lawyer_id,
                'amount' => $request->amount,
                'currency' => $request->currency ?? 'usd',
                'stripe_payment_intent_id' => $session->id,
                'status' => 'pending',
            ]);

            return response()->json(['url' => $session->url]);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error creating checkout session',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Confirm Payment After Redirect
     * (Webhook removed)
     */
    public function confirmPayment(Request $request)
    {
        try {
            $request->validate([
                'session_id' => 'required|string',
            ]);

            $session = Session::retrieve($request->session_id);

            // ============================
            // PAYMENT FAILED
            // ============================
            if ($session->payment_status !== 'paid') {

                $payment = StripePayment::where('stripe_payment_intent_id', $session->id)->first();

                if ($payment) {

                    // Update DB
                    $payment->update(['status' => 'failed']);
                    $payment->appointment->update(['payment_status' => 'failed']);

                    // Notify User
                    Mail::to($payment->appointment->user->email)
                        ->send(new PaymentFailedMail($payment));

                    // Notify Admin
                    Mail::to(config('services.admin.email'))
                        ->send(new AdminPaymentNotificationMail($payment));
                }

                return response()->json([
                    'message' => 'Payment not completed',
                    'status' => $session->payment_status
                ], 422);
            }

            // ============================
            // PAYMENT SUCCESS
            // ============================
            $payment = StripePayment::where('stripe_payment_intent_id', $session->id)->firstOrFail();

            if ($payment->status !== 'succeeded') {

                $payment->update([
                    'status'  => 'succeeded',
                    'paid_at' => now(),
                ]);
                $appointment = $payment->appointment;
                $payment->appointment->update(['payment_status' => 'confirmed']);

                // Email User
                Mail::to($payment->appointment->user->email)
                    ->send(new PaymentSuccessMail($payment));

                // Email Admin
                Mail::to(config('services.admin.email'))
                    ->send(new AdminPaymentNotificationMail($payment));

                // Email Lawyer
                Mail::to($appointment->lawyer->email)
                    ->send(new LawyerAppointmentNotification($appointment));
            }

            return response()->json([
                'message' => 'Payment confirmed.',
                'payment' => $payment->load(['appointment', 'lawyer'])
            ]);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error confirming payment',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get Payment Details
     */
    public function getPaymentDetails($appointmentId)
    {
        try {
            $appointment = Appointment::findOrFail($appointmentId);

            if ($appointment->user_id !== Auth::id()
                && $appointment->lawyer_id !== Auth::id()
            ) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $payment = StripePayment::with(['appointment', 'user', 'lawyer'])
                ->where('appointment_id', $appointmentId)
                ->first();

            if (!$payment) {
                return response()->json([
                    'message' => 'No payment found.',
                    'has_payment' => false,
                ], 404);
            }

            return response()->json([
                'payment' => $payment,
                'has_payment' => true,
            ]);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error retrieving payment',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * User Payments List
     */
    public function getUserPayments()
    {
        $payments = StripePayment::with(['appointment', 'lawyer'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['payments' => $payments]);
    }

    /**
     * Lawyer Payments List
     */
    public function getLawyerPayments1()
{
    $payments = StripePayment::select([
            'id',
            'amount',
            'currency',
            'stripe_payment_intent_id',
            'payment_method',
            'status',
            'paid_at',
            'created_at'
        ])
        ->where('lawyer_id', Auth::id())
        ->where('status', 'succeeded')
        ->orderBy('paid_at', 'desc')
        ->get();

    return response()->json([
        'payments' => $payments,
        'total_earned' => $payments->sum('amount'),
        'total_transactions' => $payments->count(),
    ]);
}


public function getLawyerPayments() 
{ 
    $payments = StripePayment::with(['appointment', 'user']) 
        ->where('lawyer_id', Auth::id()) 
        ->where('status', 'succeeded') 
        ->orderBy('paid_at', 'desc') ->get(); 
        
        return response()->json(
        [ 'payments' => $payments, 
           'total_earned' => $payments->sum('amount'), 
        
        ]); 
    }


    //Get all payments by admin
    public function getAllPayments()
{
    // only admin can access
    if (Auth::user()->role !== 2) {
        return response()->json(['message' => 'Access denied'], 403);
    }

    $payments = StripePayment::with(['user', 'lawyer', 'appointment'])
        ->orderBy('created_at', 'desc')
        ->get();

    return response()->json(['payments' => $payments]);
}


}