<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Tus public URL
    |--------------------------------------------------------------------------
    |
    | The URL on which tus is exposed by the proxy.
    | Used only if behind_proxy is set to true.
    |
    */

    'public_url' => rtrim(env('APP_URL'), '/') . '/video.uploads/',
    
    
    /*
    |--------------------------------------------------------------------------
    | Tus server Base path
    |--------------------------------------------------------------------------
    |
    | Basepath of the HTTP server
    |
    */

    'base_path' => env('TUSUPLOAD_HTTP_PATH', "/video.uploads/"),

];
