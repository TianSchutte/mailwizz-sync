<?php

namespace TianSchutte\MailwizzSync\Jobs;

use Exception;

/**
 * @package MailWizzSync
 * @licence Giant Outsourcing
 * @author: Tian Schutte
 */
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
            $this->log('Could not subscribe user to list');
            $this->release($this->releaseTime);
        }
    }

    /**
     * @param Exception $exception
     * @return void
     */
    public function failed(Exception $exception)
    {
        $this->log($exception->getMessage());
    }
}
