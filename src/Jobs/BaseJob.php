<?php

namespace TianSchutte\MailwizzSync\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use TianSchutte\MailwizzSync\Services\MailWizzService;

class BaseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $backoff = [2, 5, 10];

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 180;

    /**
     * The number of exceptions to allow before failing.
     *
     * @var int
     */
    public $maxExceptions = 3;

    /**
     * @var
     */
    protected $user;

    /**
     * @var MailWizzService
     */
    protected $mailWizzService;

    /**
     * @var Repository|Application|int|mixed
     */
    protected $releaseTime = 10;


    /**
     * @param $user
     * @param MailWizzService $mailWizzService
     */
    public function __construct($user, MailWizzService $mailWizzService)
    {
        $this->mailWizzService = $mailWizzService;
        $this->user = $user;

        $this->tries = config('mailwizzsync.queue.tries');
        $this->backoff = config('mailwizzsync.queue.backoff');
        $this->timeout = config('mailwizzsync.queue.timeout');
        $this->maxExceptions = config('mailwizzsync.queue.max_exceptions');
        $this->releaseTime = config('mailwizzsync.queue.release_time');
    }
}
