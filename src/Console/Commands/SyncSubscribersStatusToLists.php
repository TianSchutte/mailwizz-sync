<?php

namespace TianSchutte\MailwizzSync\Console\Commands;

use App\Models\User;

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

        User::chunk(self::CHUNK_SIZE, function ($users) {
            $this->info('Current Chunk Size (' . count($users) . ')');
            $this->syncSubscriberStatusToLists($users);
        });

        $this->info('Done');
        return 0;
    }

    /**
     * @param $users
     * @return void
     */
    private function syncSubscriberStatusToLists($users)
    {
        $lists = $this->mailWizzService->getLists();
        foreach ($users as $user) {
            try {
                $this->info('Syncing ' . $user->email . ' STATUS to mailwizz with ' . $user->status);

                $this->mailWizzService->updateSubscriberStatusByEmailAllLists($user, $lists);

            } catch (\Exception $e) {
                $this->error('Error syncing ' . $user->email . '. Please check mailwizz logs for more details');
                $this->logger->error($e->getMessage());
                continue;
            }
        }
    }

}
