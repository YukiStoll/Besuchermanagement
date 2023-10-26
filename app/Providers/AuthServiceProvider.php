<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */

     //Falls Probleme wieder hinzufügen
    /*protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];*/

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        Gate::define('isadmin', function ($user)
        {
            return $user->role == "Admin";
        });
        Gate::define('issuperadmin', function ($user)
        {
            return $user->role == "Super Admin";
        });
        Gate::define('isemployee', function ($user)
        {
            return $user->role == "Employee";
        });
        Gate::define('isgatekeeper', function ($user)
        {
            return $user->role == "Gatekeeper";
        });
    }
}
