<?php

namespace TianSchutte\MailwizzSync\Services;

use EmsApi\Endpoint\Lists;
use EmsApi\Endpoint\ListSubscribers;
use Illuminate\Support\Facades\Log;
use TianSchutte\MailwizzSync\Api\MailWizzApi;
use TianSchutte\MailwizzSync\Contracts\ListManagerInterface;
use TianSchutte\MailwizzSync\Contracts\SubscriberManagerInterface;
use TianSchutte\MailwizzSync\Traits\ListManagementTrait;
use TianSchutte\MailwizzSync\Traits\SubscriberManagementTrait;

/**
 * @package MailWizzApi
 * @description Main MailWizz functionality is contained here
 * @author: Tian Schutte
 */
class MailWizzService implements ListManagerInterface, SubscriberManagerInterface
{
    use SubscriberManagementTrait;
    use ListManagementTrait;

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

}
