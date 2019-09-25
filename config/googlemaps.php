<?php
return [
    /* =====================================================================
    |                                                                       |
    |                  Global Settings For Google Map                       |
    |                                                                       |
    ===================================================================== */


    /* =====================================================================
    General
    ===================================================================== */
    //Get API key: https://code.google.com/apis/console
    'key' => env('GOOGLE_MAPS_API_KEY', 'AIzaSyCQiKcVN9D44jyqrNRUtvLHh8oVvfw9IPM'),
    'adsense_publisher_id' => env('GOOGLE_ADSENSE_PUBLISHER_ID', ''), //Google AdSense publisher ID

    'geocode' => [
        'cache' => false, //Geocode caching into database
        'table_name' => 'gmaps_geocache', //Geocode caching database table name
    ],

    'css_class' => '', //Your custom css class

    'http_proxy' => env('HTTP_PROXY', null), // Proxy host and port
];
