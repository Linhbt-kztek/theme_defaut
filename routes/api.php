<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\Auth\LoginController;
use App\Http\Controllers\Api\v1\Auth\ConferenceRoomController;
use App\Http\Controllers\Api\v1\ScreenController;
use App\Http\Controllers\Api\v1\Auth\StaffController;
use App\Http\Controllers\Api\v1\AgencyController;
use App\Http\Controllers\Api\v1\DeviceController;
use App\Http\Controllers\Api\v1\EventController;
use App\Http\Controllers\Api\v1\IdentificationController;
use App\Http\Controllers\Api\v1\MeetingController;
use App\Http\Controllers\Api\v1\NotificationController;

Route::
        namespace('api/v1')->prefix('v1')->group(function () {



            Route::group(['prefix' => 'auth'], function () {

                Route::post('/login', [LoginController::class, 'login']);
                Route::post('/login-with-key', [LoginController::class, 'loginWithKey']);
                Route::post('/refresh', [LoginController::class, 'refreshToken']);
            });



            // ============================== PROTECTED ROUTES =========================
            Route::group(['middleware' => ['auth:api']], function () {
                // Auth
                Route::group(['prefix' => 'auth'], function () {
                    Route::post('/logout', [LoginController::class, 'logout']);
                });

            });
            // ============================== END PROTECTED ROUTES =========================
        
        });
