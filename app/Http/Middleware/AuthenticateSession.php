<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthenticateSession extends \Illuminate\Session\Middleware\AuthenticateSession
{
    public function handle($request, Closure $next)
    {
        // Web routes also accept Sanctum bearer authentication. A bearer-only
        // request has no browser session to validate or terminate.
        if (! $request->hasSession() || ! $this->auth->guard('web')->check()) {
            return $next($request);
        }
        $previous = $this->auth->getDefaultDriver();
        $this->auth->shouldUse('web');
        try {
            return parent::handle($request, $next);
        } finally {
            $this->auth->shouldUse($previous);
        }
    }

    protected function guard()
    {
        return $this->auth->guard('web');
    }

    protected function redirectTo(Request $request)
    {
        return route('auth.index');
    }
}
