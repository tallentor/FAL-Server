<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactFormMail;

class ContactController extends Controller
{
    public function store1(Request $request)
{
    try {
        // Validate input
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'token' => 'string',
        ]);

        // Verify Cloudflare Turnstile
        $verifyResponse = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
            'secret' => config('services.turnstile.secret_key'),
            'response' => $validated['token'],
            'remoteip' => $request->ip(),
        ]);

        $verification = $verifyResponse->json();

        if (!($verification['success'] ?? false)) {
            return response()->json([
                'message' => 'Turnstile verification failed.',
                'errors' => $verification,
            ], 422);
        }

        // Store contact message
        $contact = Contact::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'subject' => $validated['subject'],
            'message' => $validated['message'],
        ]);

        // Send email to admin (using config/services.php)
        Mail::to(config('services.admin.email'))->send(new ContactFormMail($contact));

        // Return success response
        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully! Thank you for contacting us.',
        ], 201);

    } catch (\Illuminate\Validation\ValidationException $e) {
        // Handle validation errors
        return response()->json([
            'success' => false,
            'message' => 'Validation failed.',
            'errors' => $e->errors(),
        ], 422);

    } catch (\Exception $e) {
        // Handle any unexpected errors
        return response()->json([
            'success' => false,
            'message' => 'An unexpected error occurred while processing your request.',
            'error' => $e->getMessage(),
        ], 500);
    }
}

public function store(Request $request)
{
    try {
        //  Validate input
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'token' => 'string|required',
        ]);

        //  Verify Cloudflare Turnstile
        $verifyResponse = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
            'secret' => config('services.turnstile.secret_key'),
            'response' => $validated['token'],
            'remoteip' => $request->ip(),
        ]);

        $verification = $verifyResponse->json();

        if (!($verification['success'] ?? false)) {
            return response()->json([
                'success' => false,
                'message' => 'Turnstile verification failed.',
                'errors' => $verification,
            ], 422);
        }

        //  Store contact message
        $contact = Contact::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'subject' => $validated['subject'],
            'message' => $validated['message'],
        ]);

        //  Send email to admin
        Mail::to(config('services.admin.email'))->send(new ContactFormMail($contact));

        //  Send confirmation email to user
        Mail::to($validated['email'])->send(new \App\Mail\UserContactConfirmationMail($contact));

        //  Return success response
        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully! A confirmation email has been sent to your inbox.',
        ], 201);

    } catch (\Illuminate\Validation\ValidationException $e) {
        // Handle validation errors
        return response()->json([
            'success' => false,
            'message' => 'Validation failed.',
            'errors' => $e->errors(),
        ], 422);

    } catch (\Exception $e) {
        // Handle any unexpected errors
        return response()->json([
            'success' => false,
            'message' => 'An unexpected error occurred while processing your request.',
            'error' => $e->getMessage(),
        ], 500);
    }
}


}
