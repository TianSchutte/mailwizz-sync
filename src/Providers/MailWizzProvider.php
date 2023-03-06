<?php

namespace TianSchutte\MailwizzSync\Providers;

use Illuminate\Support\ServiceProvider;
use TianSchutte\MailwizzSync\Console\Commands\BulkSyncSubscribers;
use TianSchutte\MailwizzSync\Console\Commands\ExportUsers;
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
        $this->configurePlayerStatusHistory();
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
                ExportUsers::class,
                BulkSyncSubscribers::class,
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
            $userClass = config('mailwizzsync.defaults.user_class');
            return new $userClass;
        });

        app('User')::observe(UserObserver::class);
    }

    /**
     * Configure PlayerStatusHistory for the package by binding the model to package
     *
     * @return void
     */
    private function configurePlayerStatusHistory()
    {
        $this->app->bind('PlayerStatusHistory', function ($app) {
            $userClass = config('mailwizzsync.defaults.player_status_history_class');
            return new $userClass;
        });
    }
}
