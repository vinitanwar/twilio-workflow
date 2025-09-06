<?php

namespace App\Providers;

use App\Services\TwilioService;
use Illuminate\Support\ServiceProvider;
use Twilio\Rest\Client;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        $this->app->bind(Client::class, function () {
            return new Client(env('TWILIO_ACCOUNT_SID'), env('TWILIO_AUTH_TOKEN'));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
