<?php

namespace TianSchutte\MailwizzSync\Contracts;

interface SubscriberManagerInterface
{
    public function updateSubscriberStatusLists($user, $lists);

    public function isSubscriberInLists($user): bool;

    public function subscribeToList($user): bool;

    public function unsubscribeFromLists($user): bool;

    public function unSubscribeFromList($user, $listId): bool;

    public function deleteSubscriberFromList($user, $listId): bool;

    public function getSubscriberPlayerStatusOnLists($user, $lists);

    public function getSubscriber($subscriber_id, $listId);
}
