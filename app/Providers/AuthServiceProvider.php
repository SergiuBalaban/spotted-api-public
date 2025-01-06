<?php

namespace App\Providers;

use App\Models\Chat;
use App\Models\Report;
use App\Policies\ChatPolicy;
use App\Policies\ReportPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Report::class => ReportPolicy::class,
        Chat::class => ChatPolicy::class,
    ];

    public function boot(): void {}
}
