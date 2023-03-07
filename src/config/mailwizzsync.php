<?php

return [
    'mailwizz' => [
        'api_url' => env('MAILWIZZ_API_URL', 'https://base.url.com/api/index.php/'),
        'public_key' => env('MAILWIZZ_API_PUBLIC_KEY', 'api_key'),
        'cache_file_path' => env('MAILWIZZ_CACHE_FILE_PATH', storage_path('MailWizz/data/cache')),
        'lists' => [
            'ROTW' => env('MAILWIZZ_LISTUID_ROTW', 'list_1_id'),
            'AU' => env('MAILWIZZ_LISTUID_AUNZ', 'list_2_id'),
            'NZ' => env('MAILWIZZ_LISTUID_AUNZ', 'list_2_id'),
        ],
    ],
    'defaults' => [
        'user_class' => env('MAILWIZZ_USER_CLASS', config('auth.providers.users.model')),
        'player_status_history_class' => env('MAILWIZZ_STATUS_HISTORY_CLASS', \App\Models\Member\PlayerStatusHistory::class),
        'chunk_size' => env('MAILWIZZ_CHUNK_SIZE', 50),
        'csv_file_path' => env('MAILWIZZ_CSV_FILE_PATH', public_path()),
    ],
    'queue' => [
        'tries' => env('MAILWIZZ_QUEUE_TRIES', 3),
        'timeout' => env('MAILWIZZ_QUEUE_TIMEOUT', 60),
        'backoff' => env('MAILWIZZ_QUEUE_BACKOFF', [2, 5, 10]),
        'max_exceptions' => env('MAILWIZZ_QUEUE_MAX_EXCEPTIONS', 3),
        'release_time' => env('MAILWIZZ_QUEUE_RELEASE_TIME', 10),
    ]
];
