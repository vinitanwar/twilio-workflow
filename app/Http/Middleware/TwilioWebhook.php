<?php

namespace App\Http\Middleware;

use Closure;
use Twilio\Security\RequestValidator;

class TwilioWebhook
{
    public function handle($request, Closure $next)
    {
        $validator = new RequestValidator(env('TWILIO_AUTH_TOKEN'));
        $signature = $request->header('X-Twilio-Signature');
        $url = $request->fullUrl();
        $postVars = $request->all();

        if ($validator->validate($signature, $url, $postVars)) {
            return $next($request);
        }

        return response('Unauthorized', 403);
    }
}