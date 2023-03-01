<?php

namespace TianSchutte\MailwizzSync\Traits;

use Exception;

trait SubscriberManagementTrait
{

    /**
     * @param $user
     * @param $lists
     * @return void
     * @throws Exception
     */
    public function updateSubscriberStatusByEmailAllLists($user, $lists)
    {
        $listIds = array_column($lists, 'list_uid');

        $chunks = array_chunk($listIds, config('mailwizzsync.chunk_size'));

        foreach ($chunks as $chunk) {
            foreach ($chunk as $listId) {
                if ($this->isUserSubscribedToList($user)) {
                    $this->listSubscribersEndpoint->updateByEmail($listId, $user->email,
                        ['STATUS' => $user->player_status]
                    );
                }
            }
        }
    }

    /**
     * @param $user
     * @return bool
     * @throws Exception
     */
    public function isUserSubscribedToList($user): bool
    {
        $countryListId = $this->getListIdFromConfig($user->country);

        $response = $this->listSubscribersEndpoint->emailSearch($countryListId, $user->email);

//        if (!isset($response->body)) {
//            return false;
//        }

        $status = $response->body->itemAt('status');

        if ($status != 'success') {
            return false;
        }

        return true;
    }

    /**
     * @param $user
     * @return bool
     * @throws Exception
     */
    public function subscribedUserToList($user): bool
    {
        $subscriberData = [
            'EMAIL' => $user->email,
            'FNAME' => $user->name,
            'LNAME' => $user->surname,
            'STATUS' => $user->player_status,
            'COUNTRY' => $user->country,
            'CURRENCY_CODE' => $user->currency_code
        ];

        $countryListId = $this->getListIdFromConfig($user->country);

        $response = $this->listSubscribersEndpoint->create($countryListId, $subscriberData);

//        if (!isset($response->body)) {
//            return false;
//        }

        $status = $response->body->itemAt('status');

        if ($status != 'success') {
            return false;
        }

        return true;
    }

    /**
     * @param $user
     * @return bool
     * @throws Exception
     */
    public function unsubscribeUserFromAllLists($user): bool
    {

        $response = $this->listSubscribersEndpoint->unsubscribeByEmailFromAllLists($user->email);
        $status = $response->body->itemAt('status');

        if ($status != 'success') {
            return false;
        }

        return true;
    }

    /**
     * @param $user
     * @param $listId
     * @return bool
     * @throws Exception
     */
    public function unSubscribeUserFromList($user, $listId): bool
    {
        $response = $this->listSubscribersEndpoint->unsubscribeByEmail($listId, $user->email);
        $status = $response->body->itemAt('status');

        if ($status != 'success') {
            return false;
        }

        return true;
    }

    /**
     * @param $user
     * @param $listId
     * @return bool
     * @throws Exception
     */
    public function deleteSubscriberFromList($user, $listId): bool
    {
        $response = $this->listSubscribersEndpoint->deleteByEmail($listId, $user->email);
        $status = $response->body->itemAt('status');

        if ($status != 'success') {
            return false;
        }

        return true;
    }
}
