<?php

namespace TianSchutte\MailwizzSync\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use TianSchutte\MailwizzSync\Api\MailWizzApi;
use TianSchutte\MailwizzSync\Services\MailWizzService;

/**
 * @package MailWizzApi
 * @description
 * @author: Tian Schutte
 */
class MailWizzController extends Controller
{
    protected $mailWizzService;

    public function __construct(MailWizzService $mailWizzService)
    {
        $this->mailWizzService = $mailWizzService;
    }

    public function show(Request $request)
    {
        $var = User::find(30);
        dd($this->mailWizzService->checkIfUserIsSubscribedToList($var, 'vn921lnyq5321'));
    }

    public function store(Request $request)
    {

    }

    public function destroy(Request $request)
    {

    }
}

