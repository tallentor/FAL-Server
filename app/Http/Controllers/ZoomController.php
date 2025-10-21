<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ZoomController extends Controller
{
    // Step 1: Redirect user to Zoom authorization
    public function authorizeApp()
    {
        $authorizeUrl = "https://zoom.us/oauth/authorize?response_type=code&client_id="
            . env('ZOOM_CLIENT_ID')
            . "&redirect_uri=" . urlencode(env('ZOOM_REDIRECT_URI'));

        return redirect()->away($authorizeUrl);
    }

    // Step 2: Handle Zoom callback and save access + refresh tokens
    public function handleCallback(Request $request)
    {
        $code = $request->query('code');
        $client = new Client();

        $response = $client->post('https://zoom.us/oauth/token', [
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode(env('ZOOM_CLIENT_ID') . ':' . env('ZOOM_CLIENT_SECRET')),
            ],
            'form_params' => [
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => env('ZOOM_REDIRECT_URI'),
            ],
        ]);

        $data = json_decode($response->getBody(), true);

        // Store tokens
        Storage::put('zoom_tokens.json', json_encode([
            'access_token' => $data['access_token'],
            'refresh_token' => $data['refresh_token'],
            'expires_in' => now()->addSeconds($data['expires_in']),
        ]));

        return response()->json(['message' => 'Zoom authorized successfully', 'data' => $data]);
    }

    // Get valid token (auto-refresh if expired)
    public static function getAccessToken()
    {
        if (!Storage::exists('zoom_tokens.json')) {
            return null;
        }

        $tokens = json_decode(Storage::get('zoom_tokens.json'), true);

        // If token expired, refresh it
        if (now()->greaterThan($tokens['expires_in'])) {
            $client = new Client();

            $response = $client->post('https://zoom.us/oauth/token', [
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode(env('ZOOM_CLIENT_ID') . ':' . env('ZOOM_CLIENT_SECRET')),
                ],
                'form_params' => [
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $tokens['refresh_token'],
                ],
            ]);

            $data = json_decode($response->getBody(), true);

            $tokens = [
                'access_token' => $data['access_token'],
                'refresh_token' => $data['refresh_token'],
                'expires_in' => now()->addSeconds($data['expires_in']),
            ];

            Storage::put('zoom_tokens.json', json_encode($tokens));
        }

        return $tokens['access_token'];
    }
}
