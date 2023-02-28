<?php

namespace TianSchutte\MailwizzSync\Observers;

use Illuminate\Support\Facades\App;
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
        if ($this->isUserModel($user)) {
            $this->mailwizzService->subscribedUserToList($user);
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
        if ($this->isUserModel($user)) {
            if ($user->isDirty('status')) {
                $lists = $this->mailwizzService->getLists();
                $this->mailwizzService->updateSubscriberStatusByEmailAllLists($user, $lists);
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
        if ($this->isUserModel($user)) {
            $this->mailwizzService->unsubscribeUserFromAllLists($user);
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
        if ($this->isUserModel($user)) {
            $this->mailwizzService->subscribedUserToList($user);
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
        if ($this->isUserModel($user)) {
            $this->mailwizzService->unsubscribeUserFromAllLists($user);
        }
    }

    /**
     * @param $user
     * @return bool
     */
    private function isUserModel($user): bool
    {
        $userModel = App::make('User');

        if ($user instanceof $userModel) {
            return true;
        }

        return false;
    }
}
