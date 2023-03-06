<?php

namespace TianSchutte\MailwizzSync\Observers;

use Exception;
use ReflectionException;
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
        $isSubscribeToList = false;

        try {
            $isSubscribeToList = $this->mailwizzService->subscribeToList($user);
        } catch (Exception $e) {
            logger()->error($e->getMessage());
        }

        if (!$isSubscribeToList) {
            logger()->error(
                'MailWizz: Could not subscribe user to list', [
                    'user' => $user->email,
                ]
            );
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
        if ($user->isDirty('player_status')) {
            try {
                $lists = $this->mailwizzService->getLists();
                $this->mailwizzService->updateSubscriberStatusLists($user, $lists);
            } catch (ReflectionException|Exception $e) {
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
        $isUnsubscribeFromLists = false;

        try {
            $isUnsubscribeFromLists = $this->mailwizzService->unsubscribeFromLists($user);
        } catch (Exception $e) {
            logger()->error($e->getMessage());
        }

        if (!$isUnsubscribeFromLists) {
            logger()->error(
                'MailWizz: Could not subscribe user to list', [
                    'user' => $user->email,
                ]
            );
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
        $isSubscribeToList = false;

        try {
            $isSubscribeToList = $this->mailwizzService->subscribeToList($user);
        } catch (Exception $e) {
            logger()->error($e->getMessage());
        }

        if (!$isSubscribeToList) {
            logger()->error(
                'MailWizz: Could not subscribe user to list', [
                    'user' => $user->email,
                ]
            );
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
        $isUnsubscribedFromList = false;

        try {
            $isUnsubscribedFromList = $this->mailwizzService->unsubscribeFromLists($user);
        } catch (Exception $e) {
            logger()->error($e->getMessage());
        }

        if (!$isUnsubscribedFromList) {
            logger()->error(
                'MailWizz: Could not subscribe user to list', [
                    'user' => $user->email,
                ]
            );
        }
    }
}
