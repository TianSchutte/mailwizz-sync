<?php

namespace TianSchutte\MailwizzSync\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use TianSchutte\MailwizzSync\Services\MailWizzService;

/**
 * @package MailWizzApi
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

        app('User')::chunk(self::CHUNK_SIZE, function ($users) {
            $this->info('Current Chunk Size (' . count($users) . ')');

            $this->syncSubscribersToList($users);

        });

        $this->info('Done');
        return 0;
    }

    /**
     * @description for all users, check if user is on mailwizz, if not add
     * @param $users
     * @return void
     */
    private function syncSubscribersToList($users)
    {
        foreach ($users as $user) {
            $isSubscribed = $this->mailWizzService->checkIfUserIsSubscribedToList($user);

            if ($isSubscribed) {
                $this->info($user->email . ' already added to this list');
                continue;
            }

            try {
                $subscribed = $this->mailWizzService->subscribedUserToList($user);

                if ($subscribed) {
                    $this->info('Added ' . $user->email . ' to mailwizz with ' . $user->player_status);
                }

                continue;
            } catch (Exception $e) {
                $this->error('Failed adding ' . $user->email. '. Please check mailwizz logs for more details');;
                $this->logger->error($e->getMessage());
                continue;
            }
        }
    }
}
