<?php

return [
    'api_url' => 'http://mailwizz.test/api/',
    'public_key' => '0315502c1aa7a861cbb4f4a794d7fdb8af438804',
    'cache_file_path' => '/MailWizz/data/cache',
    'lists' => [
        'ROTW' => 'vn921lnyq5321',
        'AU' => 'ls175celo493b',
        'NZ' => 'ls175celo493b',
    ],
    'user_class' => config('auth.providers.users.model'),
    'logging' => [
        'driver' => 'single',
        'path' => storage_path('logs/mailwizzsync.log'),
        'level' => 'info',
    ],
];
