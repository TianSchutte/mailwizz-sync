<?php

namespace TianSchutte\MailwizzSync\Console\Commands;

use App\Models\User;
use Exception;
use Illuminate\Console\Command;
use TianSchutte\MailwizzSync\Services\MailWizzService;

/**
 * @package MailWizzApi
 * @author: Tian Schutte
 */
class SyncSubscribersToList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "mailwizz:sync-subscribers-list {listId}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync all users into the specified mailwizz list subscribers';

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
        $listId = $this->argument('listId');

        $this->info('Syncing All Users with MailWizz List Subscribers on list: ' . $listId);

        User::chunk(100, function ($users) use ($listId) {
            $this->info('Current Chunk Size (' . count($users) . ')');

            $this->syncSubscribersToList($users, $listId);

        });

        $this->info('Done');
        return 0;
    }

    /**
     * @description for all users, check if user is on mailwizz, if not add
     * @param $users
     * @param $listId
     * @return void
     */
    private function syncSubscribersToList($users, $listId)
    {
        foreach ($users as $user) {
            $isSubscribed = $this->mailWizzService->checkIfUserIsSubscribedToList($user, $listId);

            if (!$isSubscribed) {
                try {

                    $subscribed = $this->mailWizzService->subscribedUserToList($user, $listId);

                    if ($subscribed) {
                        $this->info('Added ' . $user->email . ' to mailwizz with ' . $user->status);
                    }

                    continue;
                } catch (Exception $e) {
                    $this->info('Failed adding ' . $user->email);
                    $this->error($e->getMessage());
                    continue;
                }
            }
            $this->info($user->email . ' already added to this list');
        }
    }


}
////check if user has a base status of Unsubscribed, if so don't add to list
//if ($curSubscriber->body->toArray()['data']['status'] != 'Unsubscribed') {
//}
