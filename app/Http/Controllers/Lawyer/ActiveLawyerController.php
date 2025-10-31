<?php

namespace App\Http\Controllers\Lawyer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;

class ActiveLawyerController extends Controller
{
    public function getActiveLawyers()
    {
        $threshold = Carbon::now()->subMinutes(5);

        $activeLawyers = User::where('role', 1)
            ->whereNotNull('last_activity')
            ->where('last_activity', '>=', $threshold)
            ->select('id', 'name', 'email', 'last_activity')
            ->get();

        return response()->json([
            'success' => true,
            'active_lawyers' => $activeLawyers,
        ]);
    }
}