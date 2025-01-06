<?php

use App\Http\Middleware\Admin\AuthenticatedSuperAdmin;

Route::group(['prefix' => 'v1'], function () {
    Route::post('login', 'Auth\LoginController@login')->name('login');
    Route::post('logout', 'Auth\LoginController@logout')->name('logout');
    Route::middleware(['web', AuthenticatedSuperAdmin::class])->namespace('Admin')->group(function () {
        Route::prefix('dashboard')->name('dashboard')->group(function () {
            Route::get('statistics', 'AdminDashboardController@statistics');
        });

        Route::prefix('users')->name('users')->group(function () {
            Route::get('', 'AdminUserController@index');
            Route::prefix('{user}')->group(function () {
                Route::get('', 'AdminUserController@show');
                Route::prefix('reported-pets')->group(function () {
                    Route::get('', 'AdminUserController@reportedPets');
                });
                Route::prefix('pets')->group(function () {
                    Route::get('', 'AdminUserController@userPets');
                });
            });
        });

        Route::prefix('reported-pets')->name('pets')->group(function () {
            Route::get('', 'AdminReportController@index');
            Route::prefix('{reportedPetId}')->group(function () {
                Route::get('', 'AdminReportController@show');
                Route::delete('', 'AdminReportController@deleteReportedPet');
            });
        });

        Route::prefix('pets')->name('pets')->group(function () {
            Route::get('', 'AdminPetController@index');
            Route::prefix('{petId}')->group(function () {
                Route::get('', 'AdminPetController@show');
            });
        });
    });
});
