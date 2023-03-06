<?php

namespace TianSchutte\MailwizzSync\Console\Commands;


/**
 * @package MailWizzApi
 * @author: Tian Schutte
 */
class ViewLists extends BaseCommand
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
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $lists = $this->getLists();

        $this->info('All Current MailWizz Lists:');

        foreach ($lists as $list) {
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
