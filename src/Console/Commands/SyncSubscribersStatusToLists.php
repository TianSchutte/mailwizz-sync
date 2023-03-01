<?php

namespace TianSchutte\MailwizzSync\Console\Commands;

use Exception;
use ReflectionException;

/**
 * @package MailWizzApi
 * @author: Tian Schutte
 */
class SyncSubscribersStatusToLists extends BaseCommand
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mailwizz:sync-subscribers-status-lists';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Syncs the statuses of users on the app with the statuses of the users on all mailwizz lists';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Syncing All Users Statuses with All MailWizz List Subscribers Statuses');

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

        $this->syncSubscriberStatusToLists($lists);

        $this->info('Done');
        return 0;
    }

    /**
     * @param $lists
     * @return void
     */
    private function syncSubscriberStatusToLists($lists)
    {
        app('User')::chunk($this->chunkSize, function ($users) use ($lists) {
//            must keep try catch inside the loop, otherwise it will stop on error, and not continue with rest of users?
            foreach ($users as $user) {
                try {
                    $this->info('Syncing ' . $user->email . ' STATUS to mailwizz with ' . $user->player_status);

                    $this->mailWizzService->updateSubscriberStatusLists($user, $lists);

                } catch (Exception $e) {
                    $this->error('Error syncing ' . $user->email . '. Please check logs for more details');
                    $this->logger->error($e->getMessage());
                    continue;
                }
            }
        });
    }
}
