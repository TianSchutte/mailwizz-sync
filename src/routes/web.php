<?php

use EmsApi\Endpoint\ListSubscribers;
use Illuminate\Support\Facades\Route;
use TianSchutte\MailwizzSync\Controllers\MailWizzController;


Route::get('/mailtest/', [MailWizzController::class, 'show']);
