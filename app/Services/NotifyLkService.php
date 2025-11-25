<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class NotifyLkService
{
    protected $userId;
    protected $apiKey;
    protected $senderId;

    public function __construct()
    {
        $this->userId = config('services.notifylk.user_id');
        $this->apiKey = config('services.notifylk.api_key');
        $this->senderId = config('services.notifylk.sender_id');
    }

    public function sendSms($to, $message)
    {
        $response = Http::get('https://app.notify.lk/api/v1/send', [
            'user_id' => $this->userId,
            'api_key' => $this->apiKey,
            'sender_id' => $this->senderId,
            'to' => $to, // must be 94XXXXXXXXX
            'message' => $message,
        ]);

        return $response->json();
    }
}