<?php

namespace TianSchutte\MailwizzSync\Jobs;

use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use ReflectionException;
use TianSchutte\MailwizzSync\Services\MailWizzService;

class UpdatePlayerStatusToListsJob implements ShouldQueue
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
        try {
            $lists = $this->mailWizzService->getLists();
            $this->mailWizzService->updateSubscriberStatusLists($this->user, $lists);
        } catch (ReflectionException|Exception $e) {
            logger()->error(
                'MailWizz: Could not update user player_status to lists', [
                    'message' => $e->getMessage(),
                ]
            );
        }
    }
}
