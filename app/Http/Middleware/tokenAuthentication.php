<?php

namespace App\Http\Middleware;

use Closure;

class tokenAuthentication
{
    public function handle($request, Closure $next)
    {
        if ($request->header('it-porter-token') == env('it_porter_token')) {
            return $next($request);
        }
        else
        {

            return response()->json([
                "meta" => [
                    "error" => true,
                    "errorKey" => "NOT_AUTHORIZED",
                    "errorMsg" => "Der Token ist ungültig!",
                ],
                "result" => [

                ],
            ], 401);
        }
    }
}
