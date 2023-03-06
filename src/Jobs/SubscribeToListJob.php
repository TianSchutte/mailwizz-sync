<?php

namespace TianSchutte\MailwizzSync\Jobs;

use Exception;

class SubscribeToListJob extends BaseJob
{
    /**
     * @return void
     */
    public function handle()
    {
        $isSubscribeToList = false;

        try {
            $isSubscribeToList = $this->mailWizzService->subscribeToList($this->user);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        if (!$isSubscribeToList) {
            logger()->error(
                'MailWizz: Could not subscribe user to list', [
                    'user' => $this->user->email,
                ]
            );
            $this->release($this->releaseTime);
        }
    }

    /**
     * @param Exception $exception
     * @return void
     */
    public function failed(Exception $exception)
    {
        logger()->error(
            'MailWizz: Could not subscribe user to list', [
                'User' => $this->user->email,
                'Exception' => $exception->getMessage(),
            ]
        );
    }
}
