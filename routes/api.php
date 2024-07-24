<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ReceiptVoucherController;
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

ApiRoute::group(['namespace' => 'App\Http\Controllers'], function () {
    ApiRoute::get('purchased-module', ['as' => 'api.purchasedModule', 'uses' => 'HomeController@installedModule']);
    ApiRoute::get('get-receipt-vouchers/{iqaama_number}', ['uses' => 'HomeController@getReceiptVoucher']);
    ApiRoute::post('upload-sign', ['uses' => 'HomeController@uploadReceiptVoucherUploadSign']);


    // New driver login routes
    ApiRoute::group(['namespace' => 'Api'], function () {
        // New driver login routes
        ApiRoute::post('driver/login', ['uses' => 'ApiDriverController@login']);
        ApiRoute::get('driver/test', ['uses' => 'ApiDriverController@test']);
        // Protected driver routes
        ApiRoute::group(['middleware' => 'validate.driver'], function () {
            ApiRoute::get('driver/logout', ['uses' => 'ApiDriverController@logout']);
            ApiRoute::post('driver/check/status', ['uses' => 'ApiDriverController@driverCheckStatus']);
            ApiRoute::get('driver/current/status', ['uses' => 'ApiDriverController@driverCheckCurrentStatus']);
            // Add routes that require driver authentication here
        });
    });



});


