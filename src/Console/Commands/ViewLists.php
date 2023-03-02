<?php

namespace TianSchutte\MailwizzSync\Console\Commands;

use Exception;
use ReflectionException;

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
