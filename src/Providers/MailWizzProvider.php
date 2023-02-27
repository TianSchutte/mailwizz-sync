<?php

namespace TianSchutte\MailwizzSync\Providers;

use Illuminate\Support\ServiceProvider;
use TianSchutte\MailwizzSync\Console\Commands\SyncSubscribersStatusToLists;
use TianSchutte\MailwizzSync\Console\Commands\SyncSubscribersToLists;
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
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->mergeConfigFrom(__DIR__ . '/../config/mailwizzsync.php', 'mailwizzsync');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/mailwizzsync.php' => config_path('mailwizzsync.php'),
        ], 'config');

        $this->configureUser();
        $this->configureCommands();
//        $this->configureLogging();
    }

    /**
     * Configure logging for the package.
     *
     * @return void
     */
    private function configureLogging()
    {
        // Add the custom log channel to the list of available channels
        config(['logging.channels' => array_merge(
            config('logging.channels'), [
            'mailwizzsync' => config('mailwizzsync.logging'),
        ])]);
    }

    /**
     * Configure commands for the package.
     *
     * @return void
     */
    private function configureCommands()
    {
        // Register the command if we are using the application via the CLI
        if ($this->app->runningInConsole()) {
            $this->commands([
                SyncSubscribersToLists::class,
                SyncSubscribersStatusToLists::class,
                ViewLists::class,
            ]);
        }
    }

    /**
     * Configure user for the package.
     *
     * @return void
     */
    private function configureUser()
    {
        // Binds User Model to package
        $this->app->bind('User', function ($app) {
            $userClass = config('mailwizzsync.user_class');
            return new $userClass;
        });

        // Binds Observer to User Model
        app('User')::observe(UserObserver::class);
    }


}
