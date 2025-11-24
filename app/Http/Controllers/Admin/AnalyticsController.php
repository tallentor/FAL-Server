<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AnalyticsController extends Controller
{
    //Total appointments count
    public function getTotalAppointmentsCount()
    {
        $count = Appointment::count();

        return response()->json([
            'success' => true,
            'total_appointments' => $count
        ]);
    }

    //Total normal users count
    public function countNormalUsers()
{
    $count = User::where('role', 0)->count();

    return response()->json([
        'success' => true,
        'count' => $count
    ]);
}

//Total lawyers count
public function countLawyers()
{
    $count = User::where('role', 1)->count();

    return response()->json([
        'success' => true,
        'count' => $count
    ]);
}

//Daily appointments count
public function countTodayAppointments()
{
    // Get today's date
    $today = Carbon::today()->toDateString();

    // Count appointments where appointment_date is today
    $count = Appointment::whereDate('appointment_date', $today)->count();

    return response()->json([
        'success' => true,
        'count' => $count
    ]);
}

}
