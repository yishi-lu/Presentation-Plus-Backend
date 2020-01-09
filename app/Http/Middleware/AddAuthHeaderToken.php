<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class AddAuthHeaderToken extends Middleware
{

    public function handle($request, Closure $next, ...$guards)
    {
        $cookie_name = env('AUTH_TOKEN');

        if (!$request->bearerToken()) {
            if ($request->hasCookie($cookie_name)) {
                $token = $request->cookie($cookie_name);

                $request->headers->add([
                    'Authorization' => 'Bearer ' . $token
                ]);
            }
        }
        
        return $next($request);
    }
}
