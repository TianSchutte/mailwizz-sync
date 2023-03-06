<?php

namespace TianSchutte\MailwizzSync\Jobs;

use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use TianSchutte\MailwizzSync\Services\MailWizzService;

class UnsubscribeFromListsJob implements ShouldQueue
{
    use Dispatchable;

    /**
     * @var
     */
    protected $user;

    /**
     * @var MailWizzService
     */
    protected $mailWizzService;


    /**
     * @param $user
     * @param MailWizzService $mailWizzService
     */
    public function __construct($user, MailWizzService $mailWizzService)
    {
        $this->mailWizzService = $mailWizzService;
        $this->user = $user;
    }

    /**
     * @return void
     */
    public function handle()
    {
        $isSubscribeToList = false;

        try {
            $isSubscribeToList = $this->mailWizzService->unsubscribeFromLists($this->user);
        } catch (Exception $e) {
            logger()->error($e->getMessage());
        }

        if (!$isSubscribeToList) {
            logger()->error(
                'MailWizz: Could not unsubscribe user from lists', [
                    'user' => $this->user->email,
                ]
            );
        }


    }
}
