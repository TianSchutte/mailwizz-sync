<?php

namespace TianSchutte\MailwizzSync\Jobs;

use Exception;
use ReflectionException;

class UpdatePlayerStatusToListsJob extends BaseJob
{

    /**
     * @return void
     */
    public function handle()
    {
        try {
            $lists = $this->mailWizzService->getLists();
            $this->mailWizzService->updateSubscriberStatusLists($this->user, $lists);
        } catch (ReflectionException|Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @param Exception $exception
     * @return void
     */
    public function failed(Exception $exception)
    {
        logger()->error(
            'MailWizz: Could not update user player_status to lists', [
                'User' => $this->user->email,
                'Exception' => $exception->getMessage(),
            ]
        );
    }
}
