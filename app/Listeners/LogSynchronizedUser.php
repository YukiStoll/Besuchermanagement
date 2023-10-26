<?php

namespace App\Listeners;

use Adldap\Laravel\Events\Synchronized;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class LogSynchronizedUser
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Synchronized  $event
     * @return void
     */
    public function handle(Synchronized $event)
    {

       /* if(!empty($event->user->getMobileNumber()))
        {
            $userUpdate = DB::table('users')
                ->where('objectguid','like', $event->user->getConvertedGuid())
                ->update(
                    [
                        'forename' => $event->user->getFirstName(),
                        'surname' => $event->user->getLastName(),
                        'department' => $event->user->getDepartment(),
                        'email' => $event->user->getEmail(),
                        'telephone_number' => $event->user->getTelephoneNumber(),
                        'mobile_number' => $event->user->getMobileNumber(),
                    ]
                );
        }
        else
        {
            $userUpdate = DB::table('users')
                ->where('objectguid','like', $event->user->getConvertedGuid())
                ->update(
                    [
                        'forename' => $event->user->getFirstName(),
                        'surname' => $event->user->getLastName(),
                        'department' => $event->user->getDepartment(),
                        'email' => $event->user->getEmail(),
                        'telephone_number' => $event->user->getTelephoneNumber(),
                    ]
                );
        }

        if($userUpdate)
        {
            Log::info("User '{$event->user->getDisplayName()}', '{$userUpdate}' has been successfully updated.");
        }
        else
        {
            Log::info("User '{$event->user->getDisplayName()}', '{$userUpdate}' has not been successfully updated.");
        }*/
    }
}
