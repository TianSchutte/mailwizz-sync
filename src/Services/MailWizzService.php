<?php

namespace TianSchutte\MailwizzSync\Services;

use EmsApi\Endpoint\Lists;
use EmsApi\Endpoint\ListSubscribers;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use TianSchutte\MailwizzSync\Api\MailWizzApi;

/**
 * @package MailWizzApi
 * @description Main MailWizz functionality is contained here
 * @author: Tian Schutte
 */
class MailWizzService
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

    const CHUNK_SIZE = 50;

    /**
     * @var Log
     */
    protected $logger;

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
     * @param $user
     * @param $lists
     * @return void
     */
    public function updateSubscriberStatusByEmailAllLists($user, $lists)
    {
        if (empty($lists)) {
            $lists = $this->getLists();
        }

        if ($this->isUserModel($user)) {

            $chunks = array_chunk($lists, self::CHUNK_SIZE);

            foreach ($chunks as $chunk) {
                foreach ($chunk as $list) {
                    $listId = $list['list_uid'];
                    try {
                        $isSubscribed = $this->checkIfUserIsSubscribedToList($user);

                        if ($isSubscribed) {
                            $this->listSubscribersEndpoint->updateByEmail($listId, $user->email,
                                ['STATUS' => $user->player_status]
                            );
                        }

                    } catch (Exception $e) {
                        $this->logger->error($e->getMessage());
                        continue;
                    }
                }
            }
        }
    }

    /**
     * @param $user
     * @return bool
     */
    public function checkIfUserIsSubscribedToList($user): bool
    {
        if (!$this->isUserModel($user)) {
            return false;
        }

        try {
            $countryListId = $this->getConfigCountryListId($user->country);

            $response = $this->listSubscribersEndpoint->emailSearch($countryListId, $user->email);
            $status = $response->body->itemAt('status');

            if ($status != 'success') {
                return false;
            }

            return true;
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            return false;
        }

    }

    /**
     * @param $user
     * @return bool
     */
    public function subscribedUserToList($user): bool
    {
        if (!$this->isUserModel($user)) {
            return false;
        }

        $subscriberData = [
            'EMAIL' => $user->email,
            'FNAME' => $user->name,
            'LNAME' => $user->surname,
            'STATUS' => $user->player_status,
            'COUNTRY' => $user->country,
            'CURRENCY_CODE' => $user->currency_code
        ];

        $countryListId = $this->getConfigCountryListId($subscriberData['COUNTRY']);

        try {
            $response = $this->listSubscribersEndpoint->create($countryListId, $subscriberData);
            $status = $response->body->itemAt('status');

            if ($status != 'success') {
                return false;
            }

            return true;
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            return false;

        }
    }

    /**
     * @param $country
     * @return mixed
     */
    private function getConfigCountryListId($country)
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
    public function unsubscribeUserFromAllLists($user): bool
    {
        if (!$this->isUserModel($user)) {
            return false;
        }

        try {
            $response = $this->listSubscribersEndpoint->unsubscribeByEmailFromAllLists($user->email);
            $status = $response->body->itemAt('status');

            if ($status != 'success') {
                return false;
            }

            return true;
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            return false;
        }
    }

    /**
     * @param $user
     * @param $listId
     * @return bool
     */
    public function unSubscribeUserFromList($user, $listId): bool
    {
        if (!$this->isUserModel($user)) {
            return false;
        }

        try {
            $response = $this->listSubscribersEndpoint->unsubscribeByEmail($listId, $user->email);
            $status = $response->body->itemAt('status');

            if ($status != 'success') {
                return false;
            }

            return true;
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            return false;
        }
    }

    /**
     * @param $user
     * @return bool
     */
    private function isUserModel($user): bool
    {
        $userModel = App::make('User');

        if ($user instanceof $userModel) {
            return true;
        }

        return false;
    }
}
