<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * Global HTTP middleware stack.
     * These middleware are run during every request to your application.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        // Trust proxies (useful when sitting behind load balancer / Cloudflare)
        \App\Http\Middleware\TrustProxies::class,

        // Handles CORS
        \Fruitcake\Cors\HandleCors::class,

        // Prevents maintenance requests
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,

        // Validates post size
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,

        // Trims request strings
        \App\Http\Middleware\TrimStrings::class,

        // Converts empty strings to null
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * Middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'web' => [

            \App\Http\Middleware\DebugRoutes::class,

            // Cookie encryption & queueing
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,

            // Start the session
            \Illuminate\Session\Middleware\StartSession::class,

            // Share validation errors from session to views
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,

            // CSRF protection
            \App\Http\Middleware\VerifyCsrfToken::class,

            // Route bindings substitution
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            // Apply throttle, prefer stateless middleware here
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            // If you want to use sessions for API (not recommended) add StartSession
        ],
    ];

    /**
     * Route middleware.
     * These middleware may be assigned to routes or controllers using names.
     *
     * @var array<string, class-string|string>
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'twilio.webhook' => \App\Http\Middleware\TwilioWebhook::class,

        // Example: custom middleware
        // 'role' => \App\Http\Middleware\EnsureUserHasRole::class,
        // 'check.ip' => \App\Http\Middleware\CheckIpMiddleware::class,
    ];
}
