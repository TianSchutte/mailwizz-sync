<?php

namespace TianSchutte\MailwizzSync\Observers;

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
        try {
            $this->mailwizzService->subscribedUserToList($user);
        } catch (\Exception $e) {
            logger()->error($e->getMessage());
        }
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
            try {
                $lists = $this->mailwizzService->getLists();
                $this->mailwizzService->updateSubscriberStatusByEmailAllLists($user, $lists);
            } catch (\ReflectionException|\Exception $e) {
                logger()->error($e->getMessage());
            }
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
        try {
            $this->mailwizzService->unsubscribeUserFromAllLists($user);
        } catch (\Exception $e) {
            logger()->error($e->getMessage());
        }
    }

    /**
     * Handle the User "restored" event.
     *
     * @param $user
     * @return void
     */
    public function restored($user)
    {
        try {
            $this->mailwizzService->subscribedUserToList($user);
        } catch (\Exception $e) {
            logger()->error($e->getMessage());
        }
    }

    /**
     * Handle the User "force deleted" event.
     *
     * @param $user
     * @return void
     */
    public function forceDeleted($user)
    {
        try {
            $this->mailwizzService->unsubscribeUserFromAllLists($user);
        } catch (\Exception $e) {
            logger()->error($e->getMessage());
        }
    }
}
