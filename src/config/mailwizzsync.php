<?php

return [
    'api_url' => env('MAILWIZZ_API_URL', 'https://base.url.com/api/index.php/'),
    'public_key' => env('MAILWIZZ_API_PUBLIC_KEY', 'api_key'),
    'cache_file_path' => storage_path('/MailWizz/data/cache'),
    'lists' => [
        'ROTW' => 'list_1_id',
        'AU' => 'list_2_id',
        'NZ' => 'list_2_id',
    ],
    'user_class' => config('auth.providers.users.model'),
    'chunk_size' => env('MAILWIZZ_CHUNK_SIZE', 50),
];
