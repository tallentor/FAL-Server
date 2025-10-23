<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\CaseNotification;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CasesNotificationController extends Controller
{
    // Get all approved cases
    public function getApprovedCases()
    {
        $admin = Auth::user();
        if ($admin->role != 2) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $approvedCases = CaseNotification::where('admin_id', $admin->id)
                                         ->where('type', 'approved')
                                         ->orderBy('created_at', 'desc')
                                         ->get();

        return response()->json([
            'approved_cases' => $approvedCases
        ]);
    }



    // Get all rejected cases
    public function getRejectedCases()
    {
        $admin = Auth::user();
        if ($admin->role != 2) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $rejectedCases = CaseNotification::where('admin_id', $admin->id)
                                         ->where('type', 'rejected')
                                         ->orderBy('created_at', 'desc')
                                         ->get();

        return response()->json([
            'rejected_cases' => $rejectedCases
        ]);
    }



    // View a specific notification and mark as read
    public function markAsRead($id)
    {
        $admin = Auth::user();

        $notification = CaseNotification::where('admin_id', $admin->id)
                                        ->where('id', $id)
                                        ->first();

        if (!$notification) {
            return response()->json(['message' => 'Notification not found'], 404);
        }

        // Mark as read
        $notification->status = 'read';
        $notification->save();

        return response()->json([
            'notification' => $notification
        ]);
    }



}
