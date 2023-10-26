<?php

namespace App\Http\Controllers;


use Adldap\Laravel\Facades\Adldap;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Throwable;

class UNOController extends Controller
{
    public function test(View $test)
    {
        $ldapFailed = session('ldapFailed', 'false');

        if($ldapFailed == 'false')
        {
            try
            {
                $ad = Adldap::search()->users()->findByGuid(Auth::user()->objectguid);
                $user = Auth::user();
                if(!empty($ad->givenName[0]))
                {
                    $user->forename = $ad->givenName[0];
                }
                if(!empty($ad->sn[0]))
                {
                    $user->surname = $ad->sn[0];
                }
                if(!empty($ad->mail[0]))
                {
                    $user->email = $ad->mail[0];
                }
                if(!empty($ad->department[0]))
                {
                    $user->department = $ad->department[0];
                }
                if(!empty($ad->telephoneNumber[0]))
                {
                    $user->telephone_number = $ad->telephoneNumber[0];
                }
                if(!empty($ad->mobile[0]))
                {
                    $user->mobile_number = $ad->mobile[0];
                }
                $user->save();
                if($user->forename == null || $user->surname == null || $user->email == null || $user->department == null || $user->telephone_number == null)
                {
                    return redirect()->route('profile.uno');
                }
                else
                {
                    return $test;
                }
            }
            catch (Throwable $t)
            {
                session(['ldapFailed' => 'true']);
                return $test;
            }
        }
        else
        {
            return $test;
        }
    }
}



