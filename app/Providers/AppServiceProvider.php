<?php

namespace App\Providers;

use App\Models\Report;
use App\Observers\ReportObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->environment('local')) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }

        parent::register();
    }

    public function boot(): void
    {
        $enabledPreventLazyLoading = ! app()->isProduction() && ! app()->runningUnitTests() && config('app.preventLazyLoading');
        Model::preventLazyLoading($enabledPreventLazyLoading);
        Report::observe(ReportObserver::class);
    }
}
