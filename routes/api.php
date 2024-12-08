<?php

use App\Http\Middleware\AuthMiddleware;
use App\Http\Middleware\TwoFactorVerification;
use App\Http\Middleware\User\CheckUserBannedMiddleware;

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

Route::group(['prefix' => 'v1', 'middleware' => 'api'], function () {
    Route::get('statistics', 'PublicController@statistics')->name('statistics');
    Route::name('auth.')->group(function () {
        Route::post('verification', 'AuthController@verification')->name('verification');
        Route::post('login', 'AuthController@login')->name('login');
        Route::post('logout', 'AuthController@logout')->name('logout');
        Route::post('register', 'AuthController@register')->name('register');
        Route::post('refresh-token', 'AuthController@refresh')->name('refresh');
        Route::post('forgot-password', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('forgot-password');
        Route::post('reset-password', 'Auth\ResetPasswordController@reset')->name('reset-password');
        Route::post('resend-confirmation-email', 'AuthController@resendConfirmationEmail')->name('resend-confirmation-email');
        Route::post('confirm-email/{code}', 'AuthController@confirmEmail')->name('confirm-email');
    });
    Route::middleware(['auth:api', TwoFactorVerification::class, AuthMiddleware::class])->group(function () {
        Route::prefix('pets')->name('pets')->group(function () {
            Route::get('', 'PetController@index');
            Route::post('', 'PetController@store')->middleware(CheckUserBannedMiddleware::class);
            Route::prefix('report')->group(function () {
                Route::post('', 'PetReportController@reportedPet')->middleware(CheckUserBannedMiddleware::class);
                Route::prefix('{reportedPet}')->group(function () {
                    Route::delete('', 'PetReportController@removeReportedPet');
                    Route::patch('subscribe', 'PetReportController@subscribe');
                    Route::patch('unsubscribe', 'PetReportController@unsubscribe');
                });
            });
            Route::prefix('{pet}')->group(function () {
                Route::get('', 'PetController@show');
                Route::get('tracked-reported-pets', 'PetController@trackedReportedPets');
                Route::post('', 'PetController@update');
                Route::delete('', 'PetController@destroy');
                Route::patch('missing', 'PetReportController@missingPet');
                Route::patch('found', 'PetReportController@foundedPet');
                Route::prefix('gallery')->group(function () {
                    Route::post('', 'PetController@addGallery');
                    Route::delete('', 'PetController@removeGallery');
                });
            });
        });
        Route::prefix('profile')->group(function () {
            Route::get('', 'UserController@index');
            Route::post('', 'UserController@update');
            Route::post('send-email-verification', 'UserController@sendEmailVerification');
            Route::patch('update-email', 'UserController@updateEmail');
            Route::delete('', 'UserController@destroy');
        });
        Route::prefix('dashboard')->group(function () {
            Route::get('', 'DashboardController@index');
        });
        Route::prefix('tracked-reported-pets')->name('trackedReportedPets')->group(function () {
            Route::get('chats', 'ChatController@index');
            Route::prefix('{trackedReportedPet}')->name('trackedReportedPet')->group(function () {
                Route::patch('identical', 'TrackedReportedPetController@markAsIdentical');
                Route::patch('unsubscribe', 'TrackedReportedPetController@markAsNotIdentical');
                Route::prefix('chat')->group(function () {
                    Route::get('', 'ChatController@getChat');
                    Route::post('', 'ChatController@store');
                });
            });
        });
    });
});

Route::any('{any}', function(){
    return response()->json([
        'status'    => false,
        'message'   => 'Route not found.',
    ], 404);
})->where('any', '.*');
