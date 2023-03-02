<?php

namespace TianSchutte\MailwizzSync\Console\Commands;

use EmsApi\Endpoint\Countries;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use ReflectionException;
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

        try {
            $this->validateConnection();
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * @throws ReflectionException
     * @throws Exception
     */
    private function validateConnection()
    {
        $endpoint = new Countries();
        $response = $endpoint->getZones(1);
        if (!$this->mailWizzService->isEmsResponseSuccessful($response)) {
            throw new Exception('MailWizz API connection failed. Please check your configuration.');
        }
    }
}
