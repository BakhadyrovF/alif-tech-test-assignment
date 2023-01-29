<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\ContactEmailController;
use App\Http\Controllers\ContactPhoneController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::apiResource('contacts', ContactController::class);
Route::apiResource('contacts/{contactId}/emails', ContactEmailController::class);
Route::apiResource('contacts/{contactId}/phones', ContactPhoneController::class);
