<?php

namespace TianSchutte\MailwizzSync\Console\Commands;

use Exception;
use League\Csv\CannotInsertRecord;
use League\Csv\Writer;


class ExportUsers extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mailwizz:export-users {--countries : Export users from countries specified in config instead of rest of countries}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export users to a CSV file. add --countries boolean to only export users from countries specified in config';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $parameter = $this->option('countries');

        $filename = $parameter
            ? 'users_countries_' . time() . '.csv'
            : 'users_rotw_' . time() . '.csv';

        $filepath = config('mailwizzsync.csv_file_path') . '/' . $filename;

        $csv = Writer::createFromPath($filepath, 'w+');

        try {
            $users = $this->getUsers($parameter);
            $this->writeUsersToCsv($users, $csv);
        } catch (CannotInsertRecord|Exception $e) {
            $this->error('Error writing to CSV file or fetching users ');
            return 1;
        }

        $this->info('Users exported to ' . $csv->getPathname());
        return 0;
    }

    /**
     * @param $users
     * @param $csv
     * @return void
     * @throws CannotInsertRecord
     */
    private function writeUsersToCsv($users, $csv)
    {
        $csv->insertOne(['FNAME', 'SNAME', 'EMAIL', 'PLAYER_STATUS', 'COUNTRY', 'CURRENCY_CODE']);

        $chunks = array_chunk($users, $this->chunkSize);

        foreach ($chunks as $chunk) {
            foreach ($chunk as $user) {
                $csv->insertOne([
                    $user['name'],
                    $user['surname'],
                    $user['email'],
                    $user['player_status'],
                    $user['country'],
                    $user['currency_code'],
                ]);
            }
        }
    }

    /**
     * @param bool $parameter
     * @return mixed
     * @throws Exception
     */
    private function getUsers(bool $parameter)
    {
        $listKeys = $this->getAllListKeysExceptROTW();

        try {
            $users = app('User')
                ->select('name', 'surname', 'email', 'player_status', 'country', 'currency_code');

            $users = $parameter
                ? $users->whereIn('country', $listKeys)
                : $users->whereNotIn('country', $listKeys);

            return $users->get()->toArray();
        } catch (Exception $e) {
            throw new Exception('Error getting users: ' . $e->getMessage());
        }
    }

    /**
     * @return array
     * @throws Exception
     */
    private function getAllListKeysExceptROTW(): array
    {
        try {
            $allKeys = array_keys(config('mailwizzsync.lists'));
            return array_diff($allKeys, ['ROTW']);
        } catch (Exception $e) {
            throw new Exception('Error getting list keys: ' . $e->getMessage());
        }
    }
}
