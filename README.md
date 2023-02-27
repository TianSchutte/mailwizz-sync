# Read Me

**Task Requirements:**

- [x] Once User registers, add them to default list based on country field on User Model(ROTW, NZ/AU)
- [x] If a user is deleted through a user model, unsubscribe them from all lists/ Or Delete them from all lists
- [x] If a user status is updated, it must be updated on all lists
- [x] Command to Sync User model 'Status' fields to all mailwizz lists where user is subscribered
- [x] Command to Add all users from database to mailwizz, including 'Status' and 'Country' (Must also support assigning
  appropriately to 2 lists (ROTW, NZ/AU))
- [x] Command to view all lists, with list_id, name, description

**Setup:**

* Install MailWizz Site on a Server

```
INSTALL STEPS: https://www.mailwizz.com/kb/install-steps/  
  (Follow these only if you install a fresh copy of the app)

UPGRADE STEPS: https://www.mailwizz.com/kb/upgrade-steps/  
(Follow these only if you upgrade the app)
```

* When installing mailwizz, after installation and moved install folder
  if 404 error occurrences, Add the following to related nginx.conf

```
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

* If receiving http code 404 error when calling API only from endpoint like /lists. Make sure index.php is added to the
  end of the url. This is connected at backend/index.php/settings/index to 'Clean urls' setting. If is set to not use
  clean urls, you must include index.php otherwise leave it out.

```
    'api_url' => 'https://stgmailwizz.giantlottos.com/api/',
    'api_url' => 'https://stgmailwizz.giantlottos.com/api/index.php/',
```

* Make Sure on MailWizz side you have the following lists and fields created, the rest of details can be named whatever
  you want. The custom fields can't be done from api side.

```
-> [List]: [ListName]
    -> [TextField]: [Tag]

-> Name*: ROTW (Rest of The World)
    -> Tag*: EMAIL
    -> Tag*: FNAME
    -> Tag*: LNAME
    -> Tag*: COUNTRY
    -> Tag*: STATUS
    -> Tag*: CURRENCY_CODE
    
-> Name*: AU/NZ (Australia and New Zealand)
    -> Tag*: EMAIL
    -> Tag*: FNAME
    -> Tag*: LNAME
    -> Tag*: COUNTRY
    -> Tag*: STATUS
    -> Tag*: CURRENCY_CODE
```

* Add the package into your project
  ```composer require tianschutte/mailwizz-sync```
* Add Service Provider Call to /config/app.php

```
TianSchutte\MailwizzSync\Providers\MailWizzProvider::class,
```

* Run the following command to make alteration to config values as required

```
php artisan vendor:publish --tag=config
```

* Make sure in config/mailwizz the details are set correctly

```  
'api_url' => 'http://mailwizz.test/api/',
'public_key' => 'add_key_here',
'lists' => [
    'ROTW' => 'default_list_uid',
    'AU' => 'list_uid_here',
    'NZ' => 'list_uid_here',
    //Can add more country lists here if needed
],
'user_class' => App\Models\User::class, //add your user model path here
```

* Finally, run the following commands to migrate users to the database tables

```
php artisan mailwizz:view-lists
php artisan mailwizz:sync-subscribers-lists
php artisan mailwizz:sync-subscribers-status-lists
```
