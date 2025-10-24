<?php

return [
    /*
    |--------------------------------------------------------------------------
    | WhatsApp API Configuration
    |--------------------------------------------------------------------------
    |
    | Store the WhatsApp credentials here for sending messages.
    |
    */

    'access_token' => env('WHATSAPP_ACCESS_TOKEN', ''),
    'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID', ''),
];
