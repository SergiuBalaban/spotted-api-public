<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PetController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AuthMiddleware;
use App\Http\Middleware\Pet\PetPresenceMiddleware;
use App\Http\Middleware\Report\ReportLimitMiddleware;
use App\Http\Middleware\TwoFactorVerification;
use App\Http\Middleware\User\CheckUserBannedMiddleware;

Route::group(['prefix' => 'v1', 'middleware' => 'api'], function () {
    Route::get('statistics', 'PublicController@statistics')->name('statistics');
    Route::name('auth.')->group(function () {
        Route::post('verification', [AuthController::class, 'verification'])->name('apiPhoneVerification');
        Route::post('login', [AuthController::class, 'login'])->name('apiLogin');
    });
    Route::middleware(['auth:api', TwoFactorVerification::class, AuthMiddleware::class])->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('apiLogout');
        Route::post('refresh-token', [AuthController::class, 'refresh'])->name('apiRefresh');
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('', [UserController::class, 'getProfile'])->name('apiGetProfile');
            Route::patch('', [UserController::class, 'updateProfile'])->name('apiUpdateProfile');
            Route::delete('', [UserController::class, 'deleteProfile'])->name('apiDeleteProfile');
            //            Route::post('send-email-verification', [UserController::class, 'sendEmailVerification'])->name('apiUserSendEmailVerification');
            //            Route::patch('update-email', [UserController::class, 'updateEmail'])->name('apiPatchUserEmail');
        });

        Route::prefix('pets')->name('pets.')->group(function () {
            Route::get('', [PetController::class, 'getPets'])->name('apiGetPets');
            Route::post('', [PetController::class, 'createPet'])
                ->middleware(CheckUserBannedMiddleware::class)->name('apiCreatePet');
            Route::prefix('{pet}')->middleware(PetPresenceMiddleware::class)->group(function () {
                Route::get('', [PetController::class, 'findPetById'])->name('apiFindPetByID');
                Route::patch('', [PetController::class, 'updatePet'])->name('apiUpdatePet');
                Route::patch('status', [PetController::class, 'updatePetStatus'])->name('apiUpdatePetStatus');
                Route::delete('', [PetController::class, 'deletePet'])->name('apiDeletePet');
                Route::prefix('gallery')->name('gallery.')->group(function () {
                    Route::post('', [PetController::class, 'createPetGallery'])->name('apiCreatePetGallery');
                    Route::delete('', [PetController::class, 'deletePetGallery'])->name('apiDeletePetGallery');
                });
            });
        });

        Route::prefix('report')->name('report.')->group(function () {
            Route::post('', [ReportController::class, 'createReport'])
                ->middleware([CheckUserBannedMiddleware::class, ReportLimitMiddleware::class])
                ->name('apiCreateReportedPet');
            Route::prefix('{report}')->group(function () {
                Route::delete('', [ReportController::class, 'deleteReport'])
                    ->middleware('can:delete,report')
                    ->name('apiDeleteReportedPet');
            });
        });

        Route::prefix('chats')->name('chats.')->group(function () {
            Route::get('', [ChatController::class, 'getUserChats'])->name('apiGetUserChats');
            Route::prefix('{chat}')->group(function () {
                Route::get('', [ChatController::class, 'getUserChat'])->name('apiGetUserChat');
                Route::patch('activate', [ChatController::class, 'activateUserChat'])
                    ->middleware('can:update,chat')
                    ->name('apiActivateUserChat');
                Route::delete('', [ChatController::class, 'deleteUserChat'])
                    ->middleware('can:delete,chat')
                    ->name('apiDeleteUserChat');
                Route::post('', [ChatController::class, 'createChatMessage'])
                    ->middleware('can:createMessage,chat')
                    ->name('apiCreateUserChatMessage');
            });
        });

        Route::prefix('dashboard')->name('dashboard.')->group(function () {
            //            Route::get('', [DashboardController::class, 'index'])->name('apiGetDashboard');
            Route::get('missing-pets', [DashboardController::class, 'getMissingPets'])->name('apiGetMissingPets');
            Route::get('reports-for-missing-pet', [DashboardController::class, 'getReportsForMissingPet'])->name('apiGetReportsForMissingPets');
        });
    });
});

Route::any('{any}', function () {
    return response()->json([
        'status' => false,
        'message' => 'Route not found.',
    ], 404);
})->where('any', '.*');
