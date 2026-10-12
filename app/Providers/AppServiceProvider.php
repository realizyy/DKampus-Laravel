<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Paginasi mengikuti sistem desain (halaman admin yang memakai ->links())
        Paginator::defaultView('vendor.pagination.dkampus');

        // Important!!!!!!!!!!!!!!!!!!!!!!!!!!
        // if($this->app->environment('local')) {
        //     URL::forceScheme('https');
        // }

        // if (env('APP_ENV') !== 'local') {
        //     $this->app['request']->server->set('HTTPS', true);
        // }



    }
}
