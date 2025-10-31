<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Events\LawyerStatusUpdated;
use Carbon\Carbon;

class MarkInactiveLawyers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:mark-inactive-lawyers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark lawyers inactive if they have not been active for 5+ minutes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Threshold time for inactivity
        $threshold = Carbon::now()->subMinutes(5);

        // Get lawyers who are active but idle for more than 5 minutes
        $inactiveLawyers = User::where('role', 1) 
            ->whereNotNull('last_activity')
            ->where('last_activity', '<', $threshold)
            ->get();

        foreach ($inactiveLawyers as $lawyer) {
            // Set as inactive
            $lawyer->last_activity = null;
            $lawyer->save();

            // Broadcast event to update frontend in real time
            event(new LawyerStatusUpdated($lawyer->id, 'inactive'));
        }

        $this->info('Inactive lawyers marked: ' . $inactiveLawyers->count());
    }
}
