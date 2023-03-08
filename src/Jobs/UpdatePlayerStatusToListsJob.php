<?php

namespace TianSchutte\MailwizzSync\Jobs;

use Exception;
use ReflectionException;

/**
 * @package MailWizzSync
 * @licence Giant Outsourcing
 * @author: Tian Schutte
 */
class UpdatePlayerStatusToListsJob extends BaseMailWizzJob
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
        $this->log($exception->getMessage());
    }
}
