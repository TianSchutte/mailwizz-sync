<?php

namespace TianSchutte\MailwizzSync\Observers;

use Illuminate\Support\Facades\App;
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
        $userModel = App::make('User');

        if ($user instanceof $userModel) {
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
        $userModel = App::make('User');

        if ($user instanceof $userModel) {
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
        $userModel = App::make('User');

        if ($user instanceof $userModel) {
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
        $userModel = App::make('User');

        if ($user instanceof $userModel) {
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
        $userModel = App::make('User');

        if ($user instanceof $userModel) {
            $this->mailwizzService->unsubscribeUserFromAllLists($user);
        }
    }
}
