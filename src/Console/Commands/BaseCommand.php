<?php

namespace TianSchutte\MailwizzSync\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use TianSchutte\MailwizzSync\Services\MailWizzService;

abstract class BaseCommand extends Command
{
    /**
     * @var MailWizzService
     */
    protected $mailWizzService;

    /**
     * @var Log
     */
    protected $logger;

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
        $this->logger = logger();
        $this->chunkSize = config('mailwizzsync.chunk_size');
    }
}
