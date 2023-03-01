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
    }

    /**
     * Configure commands for the package by registering the commands if we are using the application via the CLI
     *
     * @return void
     */
    private function configureCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                SyncSubscribersToLists::class,
                SyncSubscribersStatusToLists::class,
                ViewLists::class,
            ]);
        }
    }

    /**
     * Configure user for the package by binding user model to package then observing the model
     *
     * @return void
     */
    private function configureUser()
    {
        $this->app->bind('User', function ($app) {
            $userClass = config('mailwizzsync.user_class');
            return new $userClass;
        });

        app('User')::observe(UserObserver::class);
    }
}
