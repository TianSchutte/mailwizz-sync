<?php

namespace TianSchutte\MailwizzSync\Observers;

use TianSchutte\MailwizzSync\Services\ListSubscribersService;

/**
 * @package MailWizzApi
 * @description User observer to add user to mailwizz list when created
 * @author: Tian Schutte
 */
class UserObserver
{
    /**
     * @var ListSubscribersService
     */
    protected $mailwizzService;

    /**
     * @param ListSubscribersService $mailwizzService
     */
    public function __construct(ListSubscribersService $mailwizzService)
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
        $this->mailwizzService->subscribedUserToList($user);
    }

    /**
     * Handle the User "updated" event.
     *
     * @param $user
     * @return void
     */
    public function updated($user)
    {
        if ($user->isDirty('status')) {
            $lists = $this->mailwizzService->getLists();
            $this->mailwizzService->updateSubscriberStatusByEmailAllLists($user, $lists);
        }
    }

    /**
     * Handle the User "deleted" event.
     *
     * @param $user
     * @return void
     */
    public function deleted($user)
    {
        $this->mailwizzService->unsubscribeUserFromAllLists($user);
    }

    /**
     * Handle the User "restored" event.
     *
     * @param $user
     * @return void
     */
    public function restored($user)
    {
        $this->mailwizzService->subscribedUserToList($user);
    }

    /**
     * Handle the User "force deleted" event.
     *
     * @param $user
     * @return void
     */
    public function forceDeleted($user)
    {
        $this->mailwizzService->unsubscribeUserFromAllLists($user);
    }
}
