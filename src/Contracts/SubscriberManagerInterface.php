<?php

namespace TianSchutte\MailwizzSync\Contracts;

interface SubscriberManagerInterface
{
    public function updateSubscriberStatusByEmailAllLists($user, $lists);
    public function isUserSubscribedToList($user): bool;
    public function subscribedUserToList($user): bool;
    public function unsubscribeUserFromAllLists($user): bool;
}
