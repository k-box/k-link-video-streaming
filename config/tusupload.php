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

    'public_url' => env('TUSUPLOAD_URL') ?: config('app.url') . '/video.upload',
    
    
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
