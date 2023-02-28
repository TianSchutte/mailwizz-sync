<?php

namespace TianSchutte\MailwizzSync\Services;

use EmsApi\Endpoint\Lists;
use EmsApi\Endpoint\ListSubscribers;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use TianSchutte\MailwizzSync\Api\MailWizzApi;

abstract class BaseMailWizzService
{
    /**
     * @var MailWizzApi
     */
    protected $mailwizzApi;

    /**
     * @var Lists
     */
    protected $listEndpoint;

    /**
     * @var ListSubscribers
     */
    protected $listSubscribersEndpoint;

    /**
     * @var Log
     */
    protected $logger;

    const CHUNK_SIZE = 50;

    /**
     * @param MailWizzApi $mailwizzApi
     * @param Lists $lists
     * @param ListSubscribers $listSubscribersEndpoint
     */
    public function __construct(
        MailWizzApi     $mailwizzApi,
        Lists           $lists,
        ListSubscribers $listSubscribersEndpoint
    )
    {
        $this->mailwizzApi = $mailwizzApi;
        $this->listEndpoint = $lists;
        $this->listSubscribersEndpoint = $listSubscribersEndpoint;
        $this->logger = logger();
    }

    /**
     * @return array
     */
    public function getLists(): array
    {
        $data = [];

        try {
            $response = $this->listEndpoint->getLists();

            $records = $response->body->toArray()['data']['records'];

            $chunks = array_chunk($records, self::CHUNK_SIZE);

            foreach ($chunks as $chunk) {
                foreach ($chunk as $list) {
                    $data[] = [
                        'name' => $list['general']['name'],
                        'list_uid' => $list['general']['list_uid'],
                        'display_name' => $list['general']['display_name'],
                        'description' => $list['general']['description'],
                    ];
                }
            }

        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
        }

        return $data;
    }

    /**
     * @param $country
     * @return mixed
     */
    protected function getConfigCountryListId($country)
    {
        $config = config('mailwizzsync');
        $countryValues = [];

        foreach ($config as $key => $value) {
            if (strpos($key, 'lists') !== false) {
                $countryValues = $value;
                break;
            }
        }

        return $countryValues[$country] ?? $countryValues['ROTW'];
    }

    /**
     * @param $user
     * @return bool
     */
    protected function isUserModel($user): bool
    {
        $userModel = App::make('User');

        if ($user instanceof $userModel) {
            return true;
        }

        return false;
    }
}
