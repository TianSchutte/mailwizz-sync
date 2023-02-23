<?php

namespace TianSchutte\MailwizzSync\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use TianSchutte\MailwizzSync\Services\MailWizzService;

/**
 * @package MailWizzApi
 * @author: Tian Schutte
 */
class SyncSubscribersStatusToLists extends Command
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
        $this->info('Syncing All Users Statuses with All MailWizz List Subscribers Statuses');

        User::chunk(100, function ($users) {
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
        foreach ($users as $user)
        {
            try {
                $this->info('Syncing ' . $user->email . ' STATUS to mailwizz with ' . $user->status);

                $this->mailWizzService->updateSubscriberStatusByEmailAllLists($user);

            } catch (\Exception $e) {
                $this->error('Error syncing ' . $user->email . ' STATUS to mailwizz with ' . $user->status);
                $this->error($e->getMessage());
                continue;
            }
        }
    }

}
