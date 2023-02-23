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
    protected $signature = "mailwizz:sync-subscribers-list";

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
    public function __construct(MailWizzService $mailWizzService)
    {
        parent::__construct();

        $this->mailWizzService = $mailWizzService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Syncing All Users with MailWizz List Subscribers');

        User::chunk(100, function ($users) {
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
                    $this->info('Added ' . $user->email . ' to mailwizz with ' . $user->status);
                }

                continue;
            } catch (Exception $e) {
                $this->info('Failed adding ' . $user->email);
                $this->error($e->getMessage());
                continue;
            }
        }
    }
}
