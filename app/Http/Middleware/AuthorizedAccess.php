<?php

namespace App\Http\Middleware;

use Illuminate\Http\Response;
use Closure;

class AuthorizedAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $validSecrets = explode(',', env('ALLOWED_SECRETS'));
        if (in_array($request->bearerToken(), $validSecrets)) {
            return $next($request);
        }

        abort(Response::HTTP_UNAUTHORIZED);
    }
}
