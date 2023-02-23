<?php

namespace TianSchutte\MailwizzSync\Console\Commands;

use Illuminate\Console\Command;
use TianSchutte\MailwizzSync\Services\MailWizzService;

/**
 * @package MailWizzApi
 * @author: Tian Schutte
 */
class ViewLists extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mailwizz:view-lists';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'View a list of all the lists on the mailwizz server';

    /**
     * @var MailWizzService
     */
    protected $mailWizzService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->mailWizzService = new MailWizzService();
    }


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('All Current MailWizz Lists:');

        foreach ($this->mailWizzService->getLists() as $list) {
            $this->info(
                sprintf(" - %s : %s : %s",
                    $list['list_uid'],
                    $list['name'],
                    $list['description']
                )
            );
        }

        return 0;
    }

}
////check if user has a base status of Unsubscribed, if so don't add to list
//if ($curSubscriber->body->toArray()['data']['status'] != 'Unsubscribed') {
//}
