<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;
use App;

class Localization
{
    public function handle($request, Closure $next)
    {
        if(Session::has('locale'))
        {
            App::setLocale(Session::get('locale'));
        }
        return $next($request);
    }
}
