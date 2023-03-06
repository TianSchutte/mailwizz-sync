<?php

namespace TianSchutte\MailwizzSync\Traits;

use Exception;

trait SubscriberManagementTrait
{
    /**
     * @param $user
     * @param $lists
     * @param null $status
     * @return void
     * @throws Exception
     */
    public function updateSubscriberStatusLists($user, $lists, $status = null)
    {
        if ($status == null) {
            $status = $user->player_status;
        }

        $listIds = array_column($lists, 'list_uid');

        $chunks = array_chunk($listIds, config('mailwizzsync.defaults.chunk_size'));

        foreach ($chunks as $chunk) {
            foreach ($chunk as $listId) {
                if ($this->isSubscriberInLists($user)) {
                    $this->listSubscribersEndpoint->updateByEmail($listId, $user->email,
                        [
                            'PLAYER_STATUS' => $status
                        ]
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
            'PLAYER_STATUS' => $user->player_status,
            'COUNTRY' => $user->country,
            'CURRENCY_CODE' => $user->currency_code,
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
     * @param $lists
     * @return mixed|null
     * @throws Exception
     */
    public function getSubscriberPlayerStatusOnLists($user, $lists)
    {
        $statusOnLists = [];

        foreach ($lists as $list) {
            $listId = $list['list_uid'];

            $subscriber = $this->listSubscribersEndpoint->emailSearch($listId, $user->email);

            if ($this->isEmsResponseSuccessful($subscriber)) {
                $subscriberUid = $subscriber->body->toArray()['data']['subscriber_uid'];

                $subscriber = $this->getSubscriber($subscriberUid, $listId);
                if (isset($subscriber['PLAYER_STATUS'])) {
                    $statusOnLists[] = $subscriber['PLAYER_STATUS'];
                }
            }
        }

        if (empty($statusOnLists)) {
            return null;
        }

        $allValuesAreTheSame = (count(array_unique($statusOnLists, SORT_REGULAR)) === 1);

        if (!$allValuesAreTheSame) {
            //force update of player status
            return null;
        }

        return $statusOnLists[0];
    }

    /**
     * @param $subscriber_id
     * @param $listId
     * @return false|mixed
     * @throws Exception
     */
    public function getSubscriber($subscriber_id, $listId)
    {
        $response = $this->listSubscribersEndpoint->getSubscriber($listId, $subscriber_id);

        if (!$this->isEmsResponseSuccessful($response)) {
            return false;
        }

        return $response->body->toArray()['data']['record'];
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
    public function isEmsResponseSuccessful($response): bool
    {
        $isSuccessful = $response->getHttpCode() >= 200 && $response->getHttpCode() < 400;
        if (!$isSuccessful) return false;

        $status = $response->body->itemAt('status');
        if ($status != 'success') return false;

        return true;
    }
}
