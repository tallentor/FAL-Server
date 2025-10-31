<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LawyerStatusUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $lawyerId;
    public string $status; // 'active' or 'inactive'

    public function __construct(int $lawyerId, string $status)
    {
        $this->lawyerId = $lawyerId;
        $this->status = $status;
    }

    // Public channel so all clients can listen
    public function broadcastOn()
    {
        return new Channel('lawyer-status');
    }

    public function broadcastWith()
    {
        return [
            'lawyer_id' => $this->lawyerId,
            'status' => $this->status,
            'timestamp' => now()->toDateTimeString(),
        ];
    }

    public function broadcastAs()
    {
        return 'LawyerStatusUpdated';
    }
}
