<?php

use App\Http\Controllers\Api\SmsProxyController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1/sms')->group(function () {
    Route::get('/{action}', [SmsProxyController::class, 'handle'])
         ->where('action', 'getNumber|getSms|cancelNumber|getStatus'); //Actions white List
});
