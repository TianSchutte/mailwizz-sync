<?php

return [
    'api_url' => 'https://stgmailwizz.giantlottos.com/api/index.php/',
    'public_key' => '0568bdaf25ea3f1310f7de9cb9c5a80239eaa27d',
    'cache_file_path' => storage_path('/MailWizz/data/cache'),
    'lists' => [
        'ROTW' => 'pj8651pfj4941',
        'AU' => 'gl4334yptz92f',
        'NZ' => 'gl4334yptz92f',
    ],
    'user_class' => config('auth.providers.users.model'),
    'logging' => [
        'driver' => 'single',
        'path' => storage_path('/logs/mailwizzsync.log'),
        'level' => 'info',
    ],
];
