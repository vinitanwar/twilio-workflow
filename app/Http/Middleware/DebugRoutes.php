<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DebugRoutes
{
    public function handle(Request $request, Closure $next)
    {
        Log::info('Route accessed: ' . $request->url());
        return $next($request);
    }
}