<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Opcodes\LogViewer\Facades\LogViewer;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->enforceSecureUrls();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Model::preventLazyLoading(!app()->isProduction());

        LogViewer::auth(function ($request) {
            return $request->user()
                && in_array($request->user()->email, [
                    'super.admin@test.com',
                ]);
        });
    }

    /**
     * Force HTTPS in non-local environments.
     */
    private function enforceSecureUrls(): void
    {
        if (!App::environment('local')) {
            URL::forceScheme('https');
        }
    }
}
