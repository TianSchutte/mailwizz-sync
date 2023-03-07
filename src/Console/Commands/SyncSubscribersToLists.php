<?php

namespace TianSchutte\MailwizzSync\Console\Commands;

use Exception;

/**
 * @package MailWizzSync
 * @licence Giant Outsourcing
 * @author: Tian Schutte
 */
class SyncSubscribersToLists extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "mailwizz:sync-subscribers-lists";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync all users into the specified mailwizz list subscribers';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Syncing All Users with MailWizz List Subscribers');

        try {
            $failedUsers = $this->syncSubscribersToList();
        } catch (Exception $e) {
            $this->error($e->getMessage());
            return 1;
        }

        if (!empty($failedUsers)) {
            $this->error('Failed to add the following users to mailwizz: ' . implode(', ', $failedUsers));
        }

        $this->info('Done');
        return 0;
    }

    /**
     * @description for all users, check if user is on mailwizz, if not add
     * @return array
     * @throws Exception
     */
    private function syncSubscribersToList(): array
    {
        $failedUsers = [];

        app('User')::chunk($this->chunkSize, function ($users) use (&$failedUsers) {

            foreach ($users as $user) {
                $isAlreadySubscribed = $this->mailWizzService->isSubscriberInLists($user);

                if ($isAlreadySubscribed) {
                    $this->info(sprintf("%s already added to this list", $user->email));
                    continue;
                }

                $subscribed = $this->mailWizzService->subscribeToList($user);

                if ($subscribed) {
                    $this->info(sprintf("Added %s to mailwizz with %s", $user->email, $user->player_status));

                } else {
                    $this->error(sprintf("Failed to add %s to mailwizz with %s", $user->email, $user->player_status));
                    $failedUsers[] = $user->email;
                }
            }
        });

        return $failedUsers;
    }
}
