<?php

namespace TianSchutte\MailwizzSync\Services;

use App\Models\User;
use EmsApi\Endpoint\Lists;
use EmsApi\Endpoint\ListSubscribers;
use Exception;
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
        $this->logger = Log::channel('mailwizzsync');
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
     * @param User $user
     * @param $lists
     * @return void
     */
    public function updateSubscriberStatusByEmailAllLists(User $user, $lists)
    {
        if (empty($lists)) {
            $lists = $this->getLists();
        }

        $chunks = array_chunk($lists, self::CHUNK_SIZE);

        foreach ($chunks as $chunk) {
            foreach ($chunk as $list) {
                $listId = $list['list_uid'];
                try {
                    $isSubscribed = $this->checkIfUserIsSubscribedToList($user);

                    if ($isSubscribed) {
                        $this->listSubscribersEndpoint->updateByEmail($listId, $user->email,
                            ['STATUS' => $user->status]
                        );
                    }

                } catch (Exception $e) {
                     $this->logger->error($e->getMessage());
                    continue;
                }
            }
        }
    }

    /**
     * @param User $user
     * @return bool
     */
    public function checkIfUserIsSubscribedToList(User $user): bool
    {
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
     * @param User $user
     * @return bool
     */
    public function subscribedUserToList(User $user): bool
    {
        $subscriberData = [
            'EMAIL' => $user->email,
            'FNAME' => $user->name,
            'LNAME' => $user->surname,
            'STATUS' => $user->status,
            'COUNTRY' => $user->country,
            'CURRENCY_CODE' => $user->currency_code
        ];

        $countryListId = $this->getConfigCountryListId($subscriberData['COUNTRY']);

        try {
            $this->listSubscribersEndpoint->create($countryListId, $subscriberData);
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
     * @param User $user
     * @return bool
     */
    public function unsubscribeUserFromAllLists(User $user): bool
    {
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
     * @param User $user
     * @param $listId
     * @return bool
     */
    public function unSubscribeUserFromList(User $user, $listId): bool
    {
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
}
