<?php

namespace TianSchutte\MailwizzSync\Tests;

use App\Models\User;
use Tests\TestCase;
use TianSchutte\MailwizzSync\Api\MailWizzApi;
use TianSchutte\MailwizzSync\Services\MailWizzService;

class MailWizzTest extends TestCase
{
//   RUN BY: phpunit packages/TianSchutte/mailwizz-sync/src/Tests/ --filter {method_name}

    protected $mailWizzService;

    public function testApiConnect()
    {
        $mailWizzApi = new MailWizzApi();
        $this->assertTrue($mailWizzApi->connect());
    }

//    public function testUpdateSubscriberStatusByEmailAllLists()
//    {
//        // Create a mock endpoint
//        $mockEndpoint = $this->getMockBuilder(mailWizzService::class)
//            ->disableOriginalConstructor()
//            ->getMock();
//
//        // Create a mock user object
//        $mockUser = User::find(1);
//
//        $condition = $mockEndpoint->updateSubscriberStatusByEmailAllLists($mockUser);
//
//        $this->assertNull($condition);
//    }

//    public function testCheckIfUserIsSubscribedToList(){
//        // Create a mock endpoint
//        $mockEndpoint = $this->getMockBuilder(mailWizzService::class)
//            ->disableOriginalConstructor()
//            ->getMock();
//
//        // Create a mock user object
//        $mockUser = User::find(2);
//
//        $condition = $mockEndpoint->checkIfUserIsSubscribedToList(
//            $mockUser,
//            config('mailwizzsync.default_list_id')
//        );
//
//        $this->assertTrue($condition);
//
//    }
}
