<?php

return [
    'api_url' => 'http://mailwizz.test/api/',
    'public_key' => '0315502c1aa7a861cbb4f4a794d7fdb8af438804',
    'lists' => [
        'ROTW' => 'vn921lnyq5321',
        'AU' => 'ls175celo493b',
        'NZ' => 'ls175celo493b',
    ],
    'cache_file_path' => '/MailWizz/data/cache',
    'user_class' => App\Models\User::class,
    'logging' => [
        'driver' => 'single',
        'path' => storage_path('logs/mailwizzsync.log'),
        'level' => 'info',
    ],
];
