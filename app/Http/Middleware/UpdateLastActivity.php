<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Events\LawyerStatusUpdated;
use Carbon\Carbon;

class UpdateLastActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();

            // adjust role check if your lawyer role differs
            if ($user->role == 1) {
                $wasInactive = !$user->last_activity || Carbon::now()->diffInMinutes($user->last_activity) > 5;

                $user->last_activity = now();
                $user->save();

                if ($wasInactive) {
                    event(new LawyerStatusUpdated($user->id, 'active'));
                }
            }
        }

        return $next($request);
    }
}
