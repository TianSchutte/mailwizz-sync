<?php

namespace TianSchutte\MailwizzSync\Observers;

use App\Models\User;
use TianSchutte\MailwizzSync\Services\MailWizzService;

/**
 * @package MailWizzApi
 * @description User observer to add user to mailwizz list when created
 * @author: Tian Schutte
 */
class UserObserver
{
    protected $mailwizzService;

    protected $defaultListId;

    public function __construct(MailWizzService $mailwizzService)
    {
//        TODO: determine which list to create users to (ROTW, AU/NZ)
        $this->mailwizzService = $mailwizzService;
        $this->defaultListId = config('mailwizzsync.default_list_id');
    }

    /**
     * Handle the User "created" event.
     *
     * @param User $user
     * @return void
     */
    public function created(User $user)
    {
        $this->mailwizzService->subscribedUserToList($user, $this->defaultListId);
    }

    /**
     * Handle the User "updated" event.
     *
     * @param User $user
     * @return void
     */
    public function updated(User $user)
    {
        if ($user->isDirty('status')) {
            $lists = $this->mailwizzService->getLists();
            $this->mailwizzService->updateSubscriberStatusByEmailAllLists($user, $lists);
        }
    }

    /**
     * Handle the User "deleted" event.
     *
     * @param User $user
     * @return void
     */
    public function deleted(User $user)
    {
        $this->mailwizzService->unsubscribeUserFromAllLists($user);
    }

    /**
     * Handle the User "restored" event.
     *
     * @param User $user
     * @return void
     */
    public function restored(User $user)
    {
        $this->mailwizzService->subscribedUserToList($user, $this->defaultListId);
    }

    /**
     * Handle the User "force deleted" event.
     *
     * @param User $user
     * @return void
     */
    public function forceDeleted(User $user)
    {
        $this->mailwizzService->unsubscribeUserFromAllLists($user);
    }
}
