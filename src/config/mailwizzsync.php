<?php

return [
    'api_url' => 'https://base.url.com/api/index.php/',
    'public_key' => 'api_key',
    'cache_file_path' => storage_path('/MailWizz/data/cache'),
    'lists' => [
        'ROTW' => 'list_1_id',
        'AU' => 'list_2_id',
        'NZ' => 'list_2_id',
    ],
    'user_class' => config('auth.providers.users.model')
];
