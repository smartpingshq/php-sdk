<?php

return [
    /*
    |--------------------------------------------------------------------------
    | SmartPings Client ID
    |--------------------------------------------------------------------------
    |
    | Your SmartPings Client ID. This is used to authenticate with the API.
    |
    */
    'client_id' => env('SMARTPINGS_CLIENT_ID'),

    /*
    |--------------------------------------------------------------------------
    | SmartPings Secret ID
    |--------------------------------------------------------------------------
    |
    | Your SmartPings Secret ID. This is used to authenticate with the API.
    |
    */
    'secret_id' => env('SMARTPINGS_SECRET_ID'),

    /*
    |--------------------------------------------------------------------------
    | SmartPings API URL
    |--------------------------------------------------------------------------
    |
    | The base URL for the SmartPings API. You shouldn't need to change this.
    |
    */
    'api_url' => env('SMARTPINGS_API_URL', 'https://api.smartpings.com/api/'),
];
