<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ZoomController;
use App\Http\Controllers\User\CaseController;
use App\Http\Controllers\SystemPromptController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\LawyerProfileController;
use App\Http\Controllers\Lawyer\LawyerCaseController;
use App\Http\Controllers\Admin\AssignLawyerController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\Admin\CasesNotificationController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register',[AuthController::class,'register']);
Route::post('/login',[AuthController::class,'login'])->name('login');
Route::post('/logout',[AuthController::class,'logout'])->middleware('auth:sanctum');


Route::get('/approve-user', [AuthController::class, 'getPendingUsers']);
Route::post('/approve-user/{id}', [AuthController::class, 'approveUser'])->middleware('auth:sanctum');
Route::delete('/reject-user/{id}', [AuthController::class, 'rejectUser'])->middleware('auth:sanctum');



Route::post('/chat/send', [ChatController::class, 'sendMessage']);

Route::apiResource('lawyer_profiles', LawyerProfileController::class);
Route::apiResource('system_prompts', SystemPromptController::class);
Route::apiResource('calendars', CalendarController::class);


// Route::middleware('auth:sanctum')->get('/profile', [ProfileController::class, 'profile']);


Route::middleware('auth:sanctum')->group(function () {
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