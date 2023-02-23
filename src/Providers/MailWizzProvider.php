<?php

namespace TianSchutte\MailwizzSync\Providers;

use App\Models\User;
use Illuminate\Support\ServiceProvider;
use TianSchutte\MailwizzSync\Console\Commands\SyncSubscribersStatusToLists;
use TianSchutte\MailwizzSync\Console\Commands\SyncSubscribersToList;
use TianSchutte\MailwizzSync\Console\Commands\ViewLists;
use TianSchutte\MailwizzSync\Observers\UserObserver;

/**
 * @package MailWizzApi
 * @description MailWizz Service Provider to load all necessary files and bind models to package
 * @author: Tian Schutte
 */
class MailWizzProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Load necessary folders
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
//        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'mailwizzsync');
//        $this->loadViewsFrom(__DIR__ . '/../resources/views/components', 'mailwizzsync');

        // Merge config folder
        $this->mergeConfigFrom(__DIR__ . '/../config/mailwizz.php', 'mailwizzsync');

        // Binds User Model to package
        $this->app->bind('User', function ($app) {
            return new User();//todo resolve frm config
        });

        // Binds Observer to User Model
        User::observe(UserObserver::class);

        // Register the command if we are using the application via the CLI
        if ($this->app->runningInConsole()) {
            $this->commands([
                SyncSubscribersToList::class,
                SyncSubscribersStatusToLists::class,
                ViewLists::class,
            ]);
        }
    }
}
