<?php

namespace TianSchutte\MailwizzSync\Console\Commands;

use Exception;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Input\InputArgument;

/**
 * @package MailWizzSync
 * @licence Giant Outsourcing
 * @author: Tian Schutte
 */
class SyncSubscribersToListsByDate extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mailwizz:sync-subscribers-lists-status-date';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bulk sync player status from a given date.  Add the date as an argument, as YYYY-MM-DD.';

    /**
     * Define the command's arguments and options.
     *
     * @return void
     */
    protected function configure()
    {
        $this->addArgument('dateFrom', InputArgument::REQUIRED, 'The date to use (YYYY-MM-DD)');
    }

    /**
     * @throws Exception
     */
    public function handle()
    {
        if (!app('PlayerStatusHistory')) {
            throw new Exception('PlayerStatusHistory model not found');
        }

        $this->info('Syncing All Users with MailWizz List Subscribers from the specified date');

        $date = $this->argument('dateFrom');

        $lists = $this->getLists();

        try {
            $this->syncSubscribersStatusToLists($lists, $date);
        } catch (Exception $e) {
            $this->error($e->getMessage());
            return 1;
        }

        return 0;
    }

    /**
     * @param $lists
     * @param $date
     * @return void
     * @throws Exception
     */
    private function syncSubscribersStatusToLists($lists, $date)
    {
        $statusHistories = app('PlayerStatusHistory')
            ->with('user')
            ->select('user_id', DB::raw('MAX(created_at) as latest_status_date'))
            ->where('created_at', '>=', $date)
            ->groupBy('user_id')
            ->latest('latest_status_date')
            ->get();


        foreach ($statusHistories as $curStatus) {

            $latestStatus = app('PlayerStatusHistory')
                ->where('user_id', $curStatus->user_id)
                ->where('created_at', $curStatus->latest_status_date)
                ->first();

            if (!$latestStatus) {
                continue;
            }

            $user = $latestStatus->user;

            try {
//                if ($latestStatus->changed_status !== $this->mailWizzService->getSubscriberPlayerStatusOnLists($user, $lists);) {
                $this->info(sprintf("Syncing %s STATUS to mailwizz with %s", $user->email, $latestStatus->changed_status));
                $this->mailWizzService->updateSubscriberStatusLists($user, $lists, $latestStatus->changed_status);
//                }

            } catch (Exception $e) {
                $this->error(sprintf("Error syncing %s. Please check logs for more details", $user->email));
                logger()->error($e->getMessage());
                continue;
            }
        }
    }
}
