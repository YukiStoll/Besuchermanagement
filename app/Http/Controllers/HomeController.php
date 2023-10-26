<?php

namespace App\Http\Controllers;


use Adldap\Laravel\Facades\Adldap;
use Illuminate\Http\Request;

class HomeController extends UNOController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $input = session()->get('input');
        return $this->test(View('home')->with('input', $input));
    }
}
