<?php

return [
    'debug_mode' => env('TELEGRAM_DEBUG_MODE', TRUE),
    'get_update_from_web_hook' => env('TELEGRAM_WEB_HOOK', FALSE),
    'is_local_url' => env('TELEGRAM_IS_LOCAL_URL', FALSE),
    'local_url' => env('TELEGRAM_LOCAL_URL', 'FALSE'),
//    'report_checker_exceptions' => env('TELEGRAM_REPORT_CHECKER_EXCEPTIONS', FALSE),
    "middlewares" => [
        "globals" => [

        ],
    ],
];