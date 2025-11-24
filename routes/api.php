<?php

use App\Models\User;
use App\Models\CalendarSlot;
use Illuminate\Http\Request;
use App\Events\TestPusherEvent;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ZoomController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\User\CaseController;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\CalendarSlotController;
use App\Http\Controllers\SystemPromptController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\LawyerProfileController;
use App\Http\Controllers\StripePaymentController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Lawyer\LawyerCaseController;
use App\Http\Controllers\Admin\AppointmentsController;
use App\Http\Controllers\Admin\AssignLawyerController;
use App\Http\Controllers\Lawyer\ActiveLawyerController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\Admin\CasesNotificationController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware(['auth:sanctum', 'verified']);

Route::post('/register',[AuthController::class,'register']);
//Route::post('/register/user', [AuthController::class, 'registerUser']);
//Route::post('/register/lawyer', [AuthController::class, 'registerLawyer']);
Route::post('/login',[AuthController::class,'login'])->name('login')->middleware('last_activity');
Route::post('/logout',[AuthController::class,'logout'])->middleware('auth:sanctum');
Route::get('/users',[AuthController::class,'getAllUsers']);
Route::delete('/users/{user}',[AuthController::class,'deleteUser'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    // Verify email notice
    Route::get('/email/verify', function (Request $request) {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(['message' => 'Already verified']);
        }

        $request->user()->sendEmailVerificationNotification();
        return response()->json(['message' => 'Verification link sent']);
    });

    // Resend verification link
    Route::post('/email/resend', function (Request $request) {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(['message' => 'Already verified']);
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json(['message' => 'Verification link resent']);
    });
});

// This route does NOT need auth:sanctum
Route::get('/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {
    $user = User::find($id);

    if (! $user) {
        return redirect('https://hotline.lk/login?message=UserNotFound');
    }

    if (! hash_equals(sha1($user->getEmailForVerification()), $hash)) {
        return redirect('https://hotline.lk/login?message=InvalidLink');
    }

    if ($user->hasVerifiedEmail()) {
        return redirect('https://hotline.lk/login?message=AlreadyVerified');
    }

    $user->markEmailAsVerified();

    // Redirect to login page after success
    return redirect('https://hotline.lk/login?message=EmailVerified');
})->name('verification.verify')->middleware('signed');


Route::get('/approve-user', [AuthController::class, 'getPendingUsers']);
Route::post('/approve-user/{id}', [AuthController::class, 'approveUser'])->middleware('auth:sanctum');
Route::delete('/reject-user/{id}', [AuthController::class, 'rejectUser'])->middleware('auth:sanctum');



Route::post('/chat/send', [ChatController::class, 'sendMessage']);


// Route::apiResource('lawyer_profiles', LawyerProfileController::class);

Route::get('/lawyer_profiles',[LawyerProfileController::class , 'index']);
Route::post('/lawyer_profiles',[LawyerProfileController::class , 'store']);

Route::get('/lawyer/{user}',[LawyerProfileController::class , 'getLawyerByUser']);


Route::apiResource('system_prompts', SystemPromptController::class);

Route::apiResource('calendars', CalendarController::class);
Route::get('/calendars/lawyers/{user}', [CalendarController::class, 'filterLawyers']);

    // Lawyer can create/update their available slots
Route::post('/calendar-slots', [CalendarSlotController::class, 'upsertSlots']);

// Delete a specific slot
Route::delete('/calendar-slots', [CalendarSlotController::class, 'deleteSlot']);

// Public route to get lawyer's available slots
Route::get('/calendar-slots/{lawyerId}', [CalendarSlotController::class, 'getSlots']);


 Route::middleware(['auth:sanctum','last_activity'])->group(function () {
    Route::get('/lawyer/profile', [LawyerProfileController::class, 'getAuthLawyerProfile']);
 });



Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/profile/update', [ProfileController::class, 'updateProfile']);
    Route::get('/profile', [ProfileController::class, 'profile']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/case/store', [CaseController::class, 'store'])->name('case.store');
    Route::get('/my-case', [CaseController::class, 'index'])->name('case.index');
    Route::get('/cases', [CaseController::class, 'AllCases'])->name('case.all');
});


Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    // Admin-only endpoints
    Route::get('/assign-lawyers', [AssignLawyerController::class, 'index']);
    Route::post('/assign-lawyer', [AssignLawyerController::class, 'assign']);
    Route::put('/assign-lawyer/{id}', [AssignLawyerController::class, 'update']);
    Route::delete('/assign-lawyer/{id}', [AssignLawyerController::class, 'destroy']);
});


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/my-cases', [LawyerCaseController::class, 'myAssignedCases']);
    Route::post('/cases/{caseId}/approve', [LawyerCaseController::class, 'approveCase']);
    Route::post('/cases/{caseId}/reject', [LawyerCaseController::class, 'rejectCase']);
});

Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/admin/cases/approved', [CasesNotificationController::class, 'getApprovedCases']);
    Route::get('/admin/cases/rejected', [CasesNotificationController::class, 'getRejectedCases']);
    Route::get('/admin/notifications/{id}', [CasesNotificationController::class, 'markAsRead']);
});


// Zoom OAuth
Route::get('/zoom/authorize', [ZoomController::class, 'authorizeApp']);
Route::get('/zoom/callback', [ZoomController::class, 'handleCallback']);

Route::post('/send-whatsapp', [LawyerCaseController::class, 'sendWhatsAppMessage']);
Route::get('/zoom/create-meeting', [ZoomController::class, 'testCreateMeeting']);



Route::middleware('auth:sanctum')->group(function () {
    Route::post('/payhere/payment', [PaymentController::class, 'createPayment'])->name('payhere.payment');
});

Route::post('/payhere/ipn', [PaymentController::class, 'handleIPN'])->name('payhere.payment.ipn');
Route::get('/payhere/success/{orderId}', [PaymentController::class, 'paymentSuccess'])->name('payhere.payment.success');
Route::get('/payhere/cancel/{orderId}', [PaymentController::class, 'paymentCancel'])->name('payhere.payment.cancel');

Route::post('/payments/create', [PaymentController::class, 'createPayment'])
        ->name('api.payments.create');

// Refund payment
Route::post('/payments/{orderId}/refund', [PaymentController::class, 'refundPayment'])
    ->name('api.payments.refund');

// Check refund status
Route::get('/payments/{orderId}/refund-status', [PaymentController::class, 'checkRefundStatus'])
    ->name('api.payments.refund.status');


Route::middleware(['auth:sanctum', 'last_activity'])->get('/lawyers', [LawyerProfileController::class, 'index']);

Route::get('/active-lawyers', [ActiveLawyerController::class, 'getActiveLawyers']);



Route::get('/test-pusher', function () {
    event(new TestPusherEvent('Hello from Laravel backend!'));
    return response()->json(['success' => true, 'message' => 'Event sent to Pusher']);
});



Route::get('/all/lawyers', [LawyerProfileController::class, 'getAllLawyers']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/appointments', [AppointmentController::class, 'storeAppointment']);
    Route::get('/appointments/lawyer', [AppointmentController::class, 'getMyAppointments']);
    Route::put('/appointments/{id}/approve', [AppointmentController::class, 'approveAppointment']);
    Route::get('/appointments/count/{user}', [AppointmentController::class, 'getSumAppointmnets']);
    Route::get('/appointments/count/today/{user}', [AppointmentController::class, 'getfilterbyToday']);
    Route::get('/appointments/approved/{user}', [AppointmentController::class, 'getApprovedAppointments']);
    Route::get('/appointments/pending/{user}', [AppointmentController::class, 'getPendingAppointments']);
});

//Route::middleware(['auth:sanctum', 'admin'])->get('/admin/appointments/approved', [AppointmentsController::class, 'getApprovedAppointments']);

Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/admin/appointments/all', [AppointmentsController::class, 'getAllAppointments']);
    // Admin notifications for appointment
    Route::get('/admin/appointments/{appointment_id}/notifications',[AppointmentsController::class, 'getNotificationsByAppointment']);
    //change status after sending whatsapp notification
    Route::put('/admin/notifications/{id}/send',[AppointmentsController::class, 'SendMassageManually']);
});

// Get lawyer's zoom meeting link for view in lawyer appointment
Route::middleware('auth:sanctum')->get(
    '/meeting/{appointment_id}/meeting-links',
    [LawyerProfileController::class, 'getZoomLink']
);

// Submit contact form
Route::post('/contact', [ContactController::class, 'store']);



// Protected routes (requires authentication)
Route::middleware('auth:sanctum')->group(function () {

    // Payment routes
    Route::prefix('payments')->group(function () {

        // Create Stripe Checkout session (redirect url)
        Route::post('/appointments/{appointmentId}/checkout',
            [StripePaymentController::class, 'createCheckoutSession']);

        // Confirm payment after redirect success
        Route::post('/confirm',
            [StripePaymentController::class, 'confirmPayment']);

        // Get payment details for an appointment
        Route::get('/appointments/{appointmentId}',
            [StripePaymentController::class, 'getPaymentDetails']);

        // Get all payments for authenticated user (as client)
        Route::get('/my-payments',
            [StripePaymentController::class, 'getUserPayments']);

        // Get all payments received by lawyer
        Route::get('/lawyer-earnings',
            [StripePaymentController::class, 'getLawyerPayments']);
    });
});


Route::middleware(['auth:sanctum'])->prefix('admin')->group(function () {
    Route::get('/payments', [StripePaymentController::class, 'getAllPayments']);
    Route::get('/payments/amount/{user}', [StripePaymentController::class, 'getLawyerEarnings']);
});


//Delete Lawyer Account
Route::delete('/lawyer/delete-account', [LawyerProfileController::class, 'deleteLawyerAccount'])
    ->middleware('auth:sanctum');



Route::middleware(['auth:sanctum', 'admin'])->group(function () {

    // Admin notifications for appointment approvals
    Route::get('/admin/notifications', [AppointmentController::class, 'getAdminNotifications']);

    // Total appointments count
    Route::get('/admin/appointments/total-count', [AnalyticsController::class, 'getTotalAppointmentsCount']);

    // Normal users count
    Route::get('/admin/users/count', [AnalyticsController::class, 'countNormalUsers']);

    // Lawyers count
    Route::get('/admin/lawyers/count', [AnalyticsController::class, 'countLawyers']);

    // Today's appointments count
    Route::get('/admin/appointments/today/count', [AnalyticsController::class, 'countTodayAppointments']);

});

//User Appointments
Route::middleware('auth:sanctum')->get('/user/appointments', [AppointmentController::class, 'getUserAppointments']);

// Admin routes for hold management

Route::post('/users/{userId}/hold', [AuthController::class, 'putUserOnHold']);
Route::post('/hold-users/{holdUserId}/restore', [AuthController::class, 'restoreUserFromHold']);
Route::get('/hold-users', [AuthController::class, 'getHoldUsers']);
