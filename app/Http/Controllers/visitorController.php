<?php

namespace App\Http\Controllers;

use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\AcceptHeader;

class visitorController extends UNOController
{

    public function __construct()
    {
        $this->middleware('auth');
    }



    public function index(Request $request)
    {
        return $this->test(View('newVisitor'));
    }
}
