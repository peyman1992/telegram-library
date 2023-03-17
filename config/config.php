<?php

return [
    'debug_mode' => env('TELEGRAM_DEBUG_MODE', TRUE),
    'get_update_from_web_hook' => env('TELEGRAM_WEB_HOOK', FALSE),
//    'report_checker_exceptions' => env('TELEGRAM_REPORT_CHECKER_EXCEPTIONS', FALSE),
    "middlewares" => [
        "globals" => [

        ],
    ],
];