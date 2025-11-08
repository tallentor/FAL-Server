<?php

namespace App\Http\Controllers;

use Log;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class ZoomController extends Controller
{
    public function getAccessToken()
{
    $client = new \GuzzleHttp\Client();

    try {
        $response = $client->post('https://zoom.us/oauth/token', [
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode(
                    config('zoom.client_id') . ':' . config('zoom.client_secret')
                ),
            ],
            'form_params' => [
                'grant_type' => 'account_credentials',
                'account_id' => config('zoom.account_id'),
            ],
        ]);

        $data = json_decode($response->getBody(), true);
        return $data['access_token'] ?? null;

    } catch (\Exception $e) {
        Log::error('Zoom Token Error: ' . $e->getMessage());
        return null;
    }
}

    public function createZoomMeetingExist($startTime, $hostName)
    {
        $client = new Client();
        $accessToken = $this->getAccessToken();

        if (!$accessToken) {
            return response()->json(['success' => false, 'message' => 'Failed to get Zoom access token'], 500);
        }

        try {
            $response = $client->post('https://api.zoom.us/v2/users/me/meetings', [
                'headers' => [
                    'Authorization' => "Bearer $accessToken",
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'topic' => "Meeting with $hostName",
                    'type' => 2,
                    'start_time' => Carbon::parse($startTime)->toIso8601String(),
                    'duration' => 60,
                    'timezone' => 'Asia/Colombo',
                    'settings' => [
                        'join_before_host' => true,
                        'host_video' => true,
                        'participant_video' => true,
                    ],
                ],
            ]);

            $data = json_decode($response->getBody(), true);
            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    public function createZoomMeetingzzzzzzzzzzzzz($startTime, $hostName)
{
    $client = new \GuzzleHttp\Client();
    $accessToken = $this->getAccessToken(); // Make sure this returns the OAuth token

    $response = $client->post('https://api.zoom.us/v2/users/me/meetings', [
        'headers' => [
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type' => 'application/json',
        ],
        'json' => [
            'topic' => "Meeting with $hostName",
            'type' => 2, // scheduled meeting
            'start_time' => $startTime->toIso8601String(),
            'duration' => 60,
            'timezone' => 'Asia/Colombo',
            'settings' => [
                'host_video' => true,
                'participant_video' => true,
            ],
        ],
    ]);

    $data = json_decode($response->getBody()->getContents(), true);

    // Return only the join_url
    return $data['join_url'] ?? null;
}





public function createZoomMeeting($startTime, $hostName)
{
    $client = new \GuzzleHttp\Client();
    $accessToken = $this->getAccessToken();

    $response = $client->post('https://api.zoom.us/v2/users/me/meetings', [
        'headers' => [
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type' => 'application/json',
        ],
        'json' => [
            'topic' => "Meeting with $hostName",
            'type' => 2,
            'start_time' => $startTime->toIso8601String(),
            'duration' => 60,
            'timezone' => 'Asia/Colombo',
            'settings' => [
                'host_video' => true,
                'participant_video' => true,
            ],
        ],
    ]);

    $data = json_decode($response->getBody()->getContents(), true);

    return [
        'join_url' => $data['join_url'] ?? null,
        'start_url' => $data['start_url'] ?? null, // host link
    ];
}



    // For quick testing
    public function testCreateMeeting()
    {
        return $this->createZoomMeeting(now()->addMinutes(10), 'Test Host');
    }
}
