<?php

namespace TianSchutte\MailwizzSync\Services;

use Exception;

/**
 * @package MailWizzApi
 * @description Main MailWizz functionality is contained here
 * @author: Tian Schutte
 */
class ListSubscribersService extends BaseMailWizzService
{

    /**
     * @param $user
     * @param $lists
     * @return void
     */
    public function updateSubscriberStatusByEmailAllLists($user, $lists)
    {
        $lists = $lists ?? $this->getLists();
        $listIds = array_column($lists, 'list_uid');

        if ($this->isUserModel($user)) {

            $chunks = array_chunk($listIds, self::CHUNK_SIZE);

            foreach ($chunks as $chunk) {
                foreach ($chunk as $listId) {

                    try {
                        $isSubscribed = $this->isUserSubscribedToList($user);

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
    public function isUserSubscribedToList($user): bool
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
     * @param $listId
     * @return bool
     */
    public function deleteSubscriberFromList($user, $listId): bool
    {
        if (!$this->isUserModel($user)) {
            return false;
        }

        try {
            $response = $this->listSubscribersEndpoint->deleteByEmail($listId, $user->email);
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
