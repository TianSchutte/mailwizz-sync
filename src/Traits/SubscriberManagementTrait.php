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
    public function updateSubscriberStatusLists($user, $lists)
    {
        $listIds = array_column($lists, 'list_uid');

        $chunks = array_chunk($listIds, config('mailwizzsync.chunk_size'));

        foreach ($chunks as $chunk) {
            foreach ($chunk as $listId) {
                if ($this->isSubscriberInLists($user)) {
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
    public function isSubscriberInLists($user): bool
    {
        $countryListId = $this->getListIdForCountry($user->country);

        $response = $this->listSubscribersEndpoint->emailSearch($countryListId, $user->email);

        if (!$this->isEmsResponseSuccessful($response)) {
            return false;
        }

        return true;
    }

    /**
     * @param $user
     * @return bool
     * @throws Exception
     */
    public function subscribeToList($user): bool
    {
        $subscriberData = [
            'EMAIL' => $user->email,
            'FNAME' => $user->name,
            'LNAME' => $user->surname,
            'STATUS' => $user->player_status,
            'COUNTRY' => $user->country,
            'CURRENCY_CODE' => $user->currency_code
        ];

        $countryListId = $this->getListIdForCountry($user->country);

        $response = $this->listSubscribersEndpoint->create($countryListId, $subscriberData);

        if (!$this->isEmsResponseSuccessful($response)) {
            return false;
        }

        return true;
    }

    /**
     * @param $user
     * @return bool
     * @throws Exception
     */
    public function unsubscribeFromLists($user): bool
    {

        $response = $this->listSubscribersEndpoint->unsubscribeByEmailFromAllLists($user->email);

        if (!$this->isEmsResponseSuccessful($response)) {
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
    public function unSubscribeFromList($user, $listId): bool
    {
        $response = $this->listSubscribersEndpoint->unsubscribeByEmail($listId, $user->email);

        if (!$this->isEmsResponseSuccessful($response)) {
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

        if (!$this->isEmsResponseSuccessful($response)) {
            return false;
        }

        return true;
    }

    /**
     * @param $response
     * @return bool
     */
    public static function isEmsResponseSuccessful($response): bool
    {
        $isSuccessful = $response->getHttpCode() >= 200 && $response->getHttpCode() < 400;
        if (!$isSuccessful) return false;

        $status = $response->body->itemAt('status');
        if ($status != 'success') return false;

        return true;
    }
}
