<?php

namespace App\Http\Controllers;

use Exception;
use Stripe\Stripe;
use App\Models\Payment;
use Stripe\PaymentIntent;
use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Models\StripePayment;
use Illuminate\Support\Facades\Auth;

class StripePaymentController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Create payment intent for an appointment
     */
    public function createPaymentIntent1(Request $request, $appointmentId)
    {
        try {
            $request->validate([
                'amount' => 'required|numeric|min:1',
            ]);

            $appointment = Appointment::with(['lawyer', 'lawyerProfile'])->findOrFail($appointmentId);

            // Check if user owns this appointment
            if ($appointment->user_id !== Auth::id()) {
                return response()->json([
                    'message' => 'Unauthorized. This appointment does not belong to you.'
                ], 403);
            }

            // Check if payment already exists for this appointment
            $existingPayment = StripePayment::where('appointment_id', $appointmentId)
                ->where('status', 'succeeded')
                ->first();

            if ($existingPayment) {
                return response()->json([
                    'message' => 'Payment already completed for this appointment.'
                ], 422);
            }

            // Create Stripe Payment Intent
            $paymentIntent = PaymentIntent::create([
                'amount' => $request->amount * 100, // in cents 
                'currency' => $request->currency ?? 'usd',
                'metadata' => [
                    'appointment_id' => $appointmentId,
                    'user_id' => Auth::id(),
                    'lawyer_id' => $appointment->lawyer_id,
                    'lawyer_name' => $appointment->lawyer->name ?? 'Unknown',
                ],
                'description' => "Appointment payment for {$appointment->case_title}",
            ]);

            // Create payment record
            $payment = StripePayment::create([
                'appointment_id' => $appointmentId,
                'user_id' => Auth::id(),
                'lawyer_id' => $appointment->lawyer_id,
                'amount' => $request->amount,
                'currency' => $request->currency ?? 'usd',
                'stripe_payment_intent_id' => $paymentIntent->id,
                'status' => 'pending',
                'payment_metadata' => [
                    'appointment_title' => $appointment->case_title,
                    'lawyer_name' => $appointment->lawyer->name ?? 'Unknown',
                ],
            ]);

            return response()->json([
                'message' => 'Payment intent created successfully',
                'client_secret' => $paymentIntent->client_secret,
                'payment_intent_id' => $paymentIntent->id,
                'payment' => $payment,
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error creating payment intent',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    // Inside StripePaymentController
public function createPaymentIntent(Request $request, $appointmentId)
{
    try {
        //  Validate request
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'currency' => 'nullable|string',
        ]);

        //  Get appointment and check ownership
        $appointment = Appointment::with(['lawyer', 'lawyerProfile'])->findOrFail($appointmentId);

        if ($appointment->user_id !== Auth::id()) {
            return response()->json([
                'message' => 'Unauthorized. This appointment does not belong to you.'
            ], 403);
        }

        //  Check if payment already succeeded
        $existingPayment = StripePayment::where('appointment_id', $appointmentId)
            ->where('status', 'succeeded')
            ->first();

        if ($existingPayment) {
            return response()->json([
                'message' => 'Payment already completed for this appointment.'
            ], 422);
        }

        //  Create Stripe PaymentIntent (backend-only, no redirect)
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        $paymentIntent = \Stripe\PaymentIntent::create([
            'amount' => $request->amount * 100, // cents
            'currency' => $request->currency ?? 'usd',
            'payment_method' => 'pm_card_visa', // Stripe test card
            'confirm' => true, // confirm immediately
            'automatic_payment_methods' => [
                'enabled' => true,
                'allow_redirects' => 'never', // important for backend-only
            ],
            'metadata' => [
                'appointment_id' => $appointmentId,
                'user_id' => Auth::id(),
                'lawyer_id' => $appointment->lawyer_id,
                'lawyer_name' => $appointment->lawyer->name ?? 'Unknown',
            ],
            'description' => "Appointment payment for {$appointment->case_title}",
        ]);

        //  Save payment in DB
        $payment = StripePayment::create([
            'appointment_id' => $appointmentId,
            'user_id' => Auth::id(),
            'lawyer_id' => $appointment->lawyer_id,
            'amount' => $request->amount,
            'currency' => $request->currency ?? 'usd',
            'stripe_payment_intent_id' => $paymentIntent->id,
            'status' => $paymentIntent->status,
            'payment_metadata' => [
                'appointment_title' => $appointment->case_title,
                'lawyer_name' => $appointment->lawyer->name ?? 'Unknown',
            ],
            'paid_at' => $paymentIntent->status === 'succeeded' ? now() : null,
        ]);

        //  Update appointment status if payment succeeded
        if ($paymentIntent->status === 'succeeded') {
            $appointment->update([
                'payment_status' => 'paid',

            ]);
        }

        //  Return response
        return response()->json([
            'message' => 'Payment processed successfully',
            'payment_status' => $paymentIntent->status,
            'payment' => $payment,
            'appointment_status' => $appointment->payment_status,
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Error processing payment',
            'error' => $e->getMessage(),
        ], 500);
    }
}



    /**
     * Confirm payment after successful Stripe payment
     */
    public function confirmPayment(Request $request, $appointmentId)
    {
        try {
            $request->validate([
                'payment_intent_id' => 'required|string',
            ]);

            $appointment = Appointment::findOrFail($appointmentId);

            // Check if user owns this appointment
            if ($appointment->user_id !== Auth::id()) {
                return response()->json([
                    'message' => 'Unauthorized.'
                ], 403);
            }

            // Retrieve the payment from database
            $payment = StripePayment::where('appointment_id', $appointmentId)
                ->where('stripe_payment_intent_id', $request->payment_intent_id)
                ->firstOrFail();

            // Retrieve payment intent from Stripe
            $paymentIntent = PaymentIntent::retrieve($request->payment_intent_id);

            if ($paymentIntent->status === 'succeeded') {
                // Update payment record
                $payment->update([
                    'status' => 'succeeded',
                    'stripe_charge_id' => $paymentIntent->charges->data[0]->id ?? null,
                    'payment_method' => $paymentIntent->charges->data[0]->payment_method_details->type ?? null,
                    'paid_at' => now(),
                ]);

                // Update appointment status
                $appointment->update([
                    'payment_status' => 'confirmed',
                ]);

                return response()->json([
                    'message' => 'Payment confirmed successfully',
                    'payment' => $payment->load(['appointment', 'lawyer']),
                ], 200);
            } else {
                $payment->update(['status' => 'failed']);

                return response()->json([
                    'message' => 'Payment was not successful',
                    'status' => $paymentIntent->status,
                ], 422);
            }

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error confirming payment',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get payment details for an appointment
     */
    public function getPaymentDetails($appointmentId)
    {
        try {
            $appointment = Appointment::findOrFail($appointmentId);

            // Check if user owns this appointment or is the lawyer
            if ($appointment->user_id !== Auth::id() && $appointment->lawyer_id !== Auth::id()) {
                return response()->json([
                    'message' => 'Unauthorized.'
                ], 403);
            }

            $payment = StripePayment::with(['appointment', 'user', 'lawyer'])
                ->where('appointment_id', $appointmentId)
                ->first();

            if (!$payment) {
                return response()->json([
                    'message' => 'No payment found for this appointment.',
                    'has_payment' => false,
                ], 404);
            }

            return response()->json([
                'payment' => $payment,
                'has_payment' => true,
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error retrieving payment details',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all payments for authenticated user (as client)
     */
    public function getUserPayments()
    {
        $payments = StripePayment::with(['appointment', 'lawyer'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'payments' => $payments,
        ], 200);
    }

    /**
     * Get all payments received by lawyer
     */
    public function getLawyerPayments()
    {
        $payments = StripePayment::with(['appointment', 'user'])
            ->where('lawyer_id', Auth::id())
            ->where('status', 'succeeded')
            ->orderBy('paid_at', 'desc')
            ->get();

        return response()->json([
            'payments' => $payments,
            'total_earned' => $payments->sum('amount'),
        ], 200);
    }

    /**
     * Webhook handler for Stripe events
     */
    public function webhook(Request $request)
    {
        $endpoint_secret = config('services.stripe.webhook_secret');

        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sig_header, $endpoint_secret);

            // Handle the event
            switch ($event->type) {
                case 'payment_intent.succeeded':
                    $paymentIntent = $event->data->object;
                    $this->handlePaymentIntentSucceeded($paymentIntent);
                    break;

                case 'payment_intent.payment_failed':
                    $paymentIntent = $event->data->object;
                    $this->handlePaymentIntentFailed($paymentIntent);
                    break;

                default:
                    // Unexpected event type
                    break;
            }

            return response()->json(['status' => 'success'], 200);

        } catch (\UnexpectedValueException $e) {
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }
    }

    private function handlePaymentIntentSucceeded($paymentIntent)
    {
        $payment = StripePayment::where('stripe_payment_intent_id', $paymentIntent->id)->first();

        if ($payment) {
            $payment->update([
                'status' => 'succeeded',
                'stripe_charge_id' => $paymentIntent->charges->data[0]->id ?? null,
                'paid_at' => now(),
            ]);

            // Update appointment status
            $payment->appointment->update(['status' => 'confirmed']);
        }
    }

    private function handlePaymentIntentFailed($paymentIntent)
    {
        $payment = StripePayment::where('stripe_payment_intent_id', $paymentIntent->id)->first();

        if ($payment) {
            $payment->update(['status' => 'failed']);
        }
    }
}
