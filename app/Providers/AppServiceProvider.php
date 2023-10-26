<?php

namespace App\Providers;

use App\Mail\visitorEMailErrorNotification;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();
        Schema::defaultStringLength(191);
        Queue::failing(function (JobFailed $event) {
			Log::debug("================================================================");
			Log::debug("E-Mail could not be send.");
            if($event->job->resolveName() === 'App\\Mail\\AdvancedRegistrationVisitor')
            {
                $pos1 = strpos($event->job->payload()['data']['command'], '"userId";s:2:"') + 14;
                $pos2 = strpos($event->job->payload()['data']['command'], '"address";s:27:"') + 16;
                $id = substr(
                    $event->job->payload()['data']['command'],
                    $pos1,
                    strpos($event->job->payload()['data']['command'], '"', $pos1) - $pos1
                );
                $visitorEMail = substr(
                    $event->job->payload()['data']['command'],
                    $pos2,
                    strpos($event->job->payload()['data']['command'], '"', $pos2) - $pos2
                );
                $email = DB::table('users')
                    ->select('email')
                    ->where('id','=', $id)
                    ->first();
                $content = DB::table("summernotes")
                    ->select('content_de')
                    ->where('id','=',8)
                    ->first();
                Log::info("E-Mail für {$email->email} wird in die Queue gesetzt.");
                Mail::to($email)
                    ->queue(new visitorEMailErrorNotification($content->content_de, $visitorEMail, $event->exception));
                Log::info("E-Mail für {$email->email} wurde in die Queue gesetzt.");
            }
			Log::debug("================================================================");
        });
    }
}
