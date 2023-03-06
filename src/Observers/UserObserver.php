<?php

namespace TianSchutte\MailwizzSync\Observers;

use TianSchutte\MailwizzSync\Jobs\SubscribeToListJob;
use TianSchutte\MailwizzSync\Jobs\UnsubscribeFromListsJob;
use TianSchutte\MailwizzSync\Jobs\UpdatePlayerStatusToListsJob;
use TianSchutte\MailwizzSync\Services\MailWizzService;

/**
 * @package MailWizzApi
 * @description User observer to add user to mailwizz list when created
 * @author: Tian Schutte
 */
class UserObserver
{
    /**
     * @var MailWizzService
     */
    protected $mailwizzService;

    /**
     * @param MailWizzService $mailwizzService
     */
    public function __construct(MailWizzService $mailwizzService)
    {
        $this->mailwizzService = $mailwizzService;
    }

    /**
     * Handle the User "created" event.
     *
     * @param $user
     * @return void
     */
    public function created($user)
    {
        SubscribeToListJob::dispatch($user, $this->mailwizzService);
    }

    /**
     * Handle the User "updated" event.
     *
     * @param $user
     * @return void
     */
    public function updated($user)
    {
        UpdatePlayerStatusToListsJob::dispatchIf
        (
            $user->isDirty('player_status'),
            $user,
            $this->mailwizzService
        );
    }

    /**
     * Handle the User "deleted" event.
     *
     * @param $user
     * @return void
     */
    public function deleted($user)
    {
        UnsubscribeFromListsJob::dispatch($user, $this->mailwizzService);
    }

    /**
     * Handle the User "restored" event.
     *
     * @param $user
     * @return void
     */
    public function restored($user)
    {
        SubscribeToListJob::dispatch($user, $this->mailwizzService);
    }

    /**
     * Handle the User "force deleted" event.
     *
     * @param $user
     * @return void
     */
    public function forceDeleted($user)
    {
        UnsubscribeFromListsJob::dispatch($user, $this->mailwizzService);
    }
}
