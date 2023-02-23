<?php

namespace TianSchutte\MailwizzSync\Services;

use App\Models\User;
use EmsApi\Endpoint\Lists;
use EmsApi\Endpoint\ListSubscribers;
use Exception;
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
    }

    /**
     * @return array
     */
    public function getLists(): array
    {
        $data = [];

        try {
            $response = $this->listEndpoint->getLists();

            $body = $response->body->toArray();

            foreach ($body['data']['records'] as $list) {
                $data[] = [
                    'name' => $list['general']['name'],
                    'list_uid' => $list['general']['list_uid'],
                    'display_name' => $list['general']['display_name'],
                    'description' => $list['general']['description'],
                ];
            }
        } catch (Exception $e) {
            logger()->error($e->getMessage());
        }

        return $data;
    }

    /**
     * @param User $user
     * @return void
     */
    public function updateSubscriberStatusByEmailAllLists(User $user)
    {
        $lists = $this->getLists();

        foreach ($lists as $list) {
            $listId = $list['list_uid'];
            try {
                $this->listSubscribersEndpoint->updateByEmail($listId, $user->email,
                    ['STATUS' => $user->status]
                );

            } catch (Exception $e) {
                logger()->error($e->getMessage());
                continue;
            }
        }
    }

    /**
     * @param User $user
     * @param $listId
     * @return bool
     */
    public function checkIfUserIsSubscribedToList(User $user, $listId): bool
    {
        try {
            $response = $this->listSubscribersEndpoint->emailSearch($listId, $user->email);
            $status = $response->body->itemAt('status');

            if ($status != 'success') {
                return false;
            }

            return true;
        } catch (Exception $e) {
            logger()->error($e->getMessage());
            return false;
        }
    }

    /**
     * @param User $user
     * @param $listId
     * @return bool
     */
    public function subscribedUserToList(User $user, $listId): bool
    {
        //TODO not sure how i'll handle different list id's, can add to config, but what about new lists?

        if (empty($listId)) {
            return false;
        }

        $subscriberData = [
            'EMAIL' => $user->email,
            'FNAME' => $user->name,
            'LNAME' => $user->surname,
            'STATUS' => $user->status,
//            'COUNTRY' => $user->country
        ];

        try {
            $this->listSubscribersEndpoint->create($listId, $subscriberData);
            return true;
        } catch (Exception $e) {
            logger()->error($e->getMessage());
            return false;

        }
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
            logger()->error($e->getMessage());
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
            logger()->error($e->getMessage());
            return false;
        }
    }
}
