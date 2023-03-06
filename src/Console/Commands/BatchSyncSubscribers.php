<?php

namespace TianSchutte\MailwizzSync\Console\Commands;

use Exception;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Input\InputArgument;

class BatchSyncSubscribers extends BaseCommand
{
    protected $signature = 'mailwizz:sync-bulk-status';

    protected $description = 'Bulk sync player status from a given date';

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
     * @throws \Exception
     */
    public function handle()
    {
        $date = $this->argument('dateFrom');

        $lists = $this->getLists();

        $this->syncSubscribersStatusToLists($lists, $date);

        return 0;
    }

    /**
     * @param $lists
     * @param $date
     * @return void
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
                $curStatus = $this->mailWizzService->getSubscriberPlayerStatusOnLists($user, $lists);
                $hasStatusChanged = $latestStatus->changed_status !== $curStatus;

                if ($hasStatusChanged) {
                    $this->info(sprintf( "Syncing %s STATUS to mailwizz with %s", $user->email, $latestStatus->changed_status));
                    $this->mailWizzService->updateSubscriberStatusLists($user, $lists, $latestStatus->changed_status);
                }

            } catch (Exception $e) {
                $this->error(sprintf("Error syncing %s. Please check logs for more details", $user->email));
                $this->logger->error($e->getMessage());
                continue;
            }
        }
    }
}
