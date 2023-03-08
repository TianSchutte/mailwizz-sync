<?php

namespace TianSchutte\MailwizzSync\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use ReflectionException;
use TianSchutte\MailwizzSync\Services\MailWizzService;

/**
 * @package MailWizzSync
 * @licence Giant Outsourcing
 * @author: Tian Schutte
 */
abstract class BaseMailWizzCommand extends Command
{
    /**
     * @var MailWizzService
     */
    protected $mailWizzService;

    /**
     * @var int
     */
    protected $chunkSize;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(MailWizzService $mailWizzService)
    {
        parent::__construct();

        $this->mailWizzService = $mailWizzService;
        $this->chunkSize = config('mailwizzsync.defaults.chunk_size');
    }

    /**
     * @return array|int
     */
    protected function getLists()
    {
        try {
            $lists = $this->mailWizzService->getLists();
        } catch (ReflectionException|Exception $e) {
            $this->error($e->getMessage());
            return 1;
        }

        if (empty($lists)) {
            $this->error('No lists found on mailwizz server');
            return 1;
        }
        return $lists;
    }
}
