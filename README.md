# MailWizz-Sync

This package provides methods and commands for managing subscribers of email lists in MailWizz. It has methods to get the email lists, update the subscriber status for all email lists, check if a user is subscribed to a list, subscribe a user to a list, unsubscribe a user from all lists, unsubscribe a user from a specific list, and delete a subscriber from a specific list. The class uses endpoints to interact with the email list management system and catches exceptions if errors occur, and logs it to the laravel log.


## Task Requirements:
### Commands Functionality:
- [x] View a list of all countries
- [x] Export Users, based on countries defined in config, to csv
- [x] Sync All Users to mailwizz from database
- [x] Sync All User statuses to mailwizz form database
- [x] Sync filtered Users status to mailwizz, that has had their roles changed, by looking in player_status_histories table
### Base Functionality:
- [x] Observer which checks whenever a user model has certain interactions, sync relavent data to mailwizz on interaction.
    - [x] Sends a job to a queue whenever one of these interactions occur
        - Once User registers, add them to default list based on country field on User Model(ROTW, NZ/AU)
        - If a user is deleted through a user model, unsubscribe them from all lists/ Or Delete them from all lists
        - If a user status is updated, it must be updated on all lists


## Installation

### MailWizz Setup
- Install MailWizz Site on a Server
- Make Sure on MailWizz side you have the following lists and fields created, the rest of details can be named whatever you want. The custom fields can't be done from api side.
```
-> [List]: [ListName]
    -> [TextField]: [Tag]

-> Name*: ROTW (Rest of The World)
    -> Tag*: EMAIL
    -> Tag*: FNAME
    -> Tag*: LNAME
    -> Tag*: COUNTRY
    -> Tag*: PLAYER_STATUS
    -> Tag*: CURRENCY_CODE
    
-> Name*: AU/NZ (Australia and New Zealand)
    -> Tag*: EMAIL
    -> Tag*: FNAME
    -> Tag*: LNAME
    -> Tag*: COUNTRY
    -> Tag*: PLAYER_STATUS
    -> Tag*: CURRENCY_CODE
```

### Package Setup
- Add package to project

```composer
composer require tianschutte/mailwizz-sync (php composer.phar require tianschutte/mailwizz-sync)
```

- In Project Driectory add the Service Provider Call in `/config/app.php`
```php
TianSchutte\MailwizzSync\Providers\MailWizzProvider::class,
```

- Run the following command to make alteration to config values as required

```php
php artisan vendor:publish --tag=config
```

- In the newly copied config file called mailwizzsync.php in `/config/mailwizzsync.php` make sure all details are set correctly
```php

return [
    'mailwizz' => [
        'api_url' => env('MAILWIZZ_API_URL', 'https://base.url.com/api/index.php/'),
        'public_key' => env('MAILWIZZ_API_PUBLIC_KEY', 'api_key'),
        'cache_file_path' => env('MAILWIZZ_CACHE_FILE_PATH', storage_path('MailWizz/data/cache')),
        'lists' => [
            'ROTW' => env('MAILWIZZ_LISTUID_ROTW', 'list_1_id'),
            'AU' => env('MAILWIZZ_LISTUID_AUNZ', 'list_2_id'),
            'NZ' => env('MAILWIZZ_LISTUID_AUNZ', 'list_2_id'),
            //Can add more country lists here if needed

        ],
    ],
    'defaults' => [
        'user_class' => env('MAILWIZZ_USER_CLASS', config('auth.providers.users.model')), //add your user model path here
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
```
- Another Option is to set the env variables in your .env file
```dotenv
MAILWIZZ_API_URL=
MAILWIZZ_API_PUBLIC_KEY=
MAILWIZZ_CACHE_FILE_PATH=storage_path('MailWizz/data/cache')
MAILWIZZ_LISTUID_ROTW=
MAILWIZZ_LISTUID_AUNZ=
MAILWIZZ_USER_CLASS=config('auth.providers.users.model')
MAILWIZZ_CHUNK_SIZE=50
MAILWIZZ_CSV_FILE_PATH=public_path()
MAILWIZZ_STATUS_HISTORY_CLASS=\App\Models\Member\PlayerStatusHistory::class
MAILWIZZ_QUEUE_TRIES=3
MAILWIZZ_QUEUE_TIMEOUT=60
MAILWIZZ_QUEUE_BACKOFF=[2,5,10]
MAILWIZZ_QUEUE_MAX_EXCEPTIONS=3
MAILWIZZ_QUEUE_RELEASE_TIME=10
```
- Queues: To allow the user observer to do its job, you must have a queue setup and running. If you don't have a queue setup, you can use the following commands to create and run the queue in the background
```bash
php artisan queue:table
php artisan migrate --path=database/migrations/2023_03_07_065701_create_jobs_table.php
php artisan queue:work 
```
- Finally, run the following commands to sync users to the mailwizz from users tables
```bash
php artisan mailwizz:sync-subscribers-lists             Sync all users into the specified mailwizz list subscribers                                                  
php artisan mailwizz:sync-subscribers-lists-status      Syncs the statuses of users on the app with the statuses of the users on all mailwizz lists                  
php artisan mailwizz:sync-subscribers-lists-status-date Bulk sync player status from a given date.  Add the date as an argument, as YYYY-MM-DD.  
php artisan mailwizz:view-lists                         View a list of all the lists on the mailwizz server                                                          
php artisan mailwizz:export-users                       Export users to a CSV file. add --countries boolean to only export users from countries specified in config  
```


## Documentation

- [MailWizz: Api Docs](https://api-docs.mailwizz.com/)
- [MailWizz: Install Steps](https://www.mailwizz.com/kb/install-steps/)
- [MailWizz: Upgrade Steps](https://www.mailwizz.com/kb/upgrade-steps/)

## Appendix

- If receiving http code 404 error when calling API only from endpoint like /lists. Make sure index.php is added to the end of the url. This is connected at backend/index.php/settings/index to 'Clean urls' setting. If is set to not use clean urls, you must include index.php otherwise leave it out.

```php
'api_url' => 'https://mailwizz.com/api/',
'api_url' => 'https://mailwizz.com/api/index.php/',
```

- When installing mailwizz, after installation and moved install folder if 404 error occurrences, Add the following to related nginx.conf

```php
if (!-e $request_filename){
    rewrite customer/.* /customer/index.php;
}

if (!-e $request_filename){
    rewrite backend/.* /backend/index.php;
}

if (!-e $request_filename){
    rewrite api/.* /api/index.php;
}

if (!-e $request_filename){
    rewrite ^(.*)$ /index.php;
}
```
