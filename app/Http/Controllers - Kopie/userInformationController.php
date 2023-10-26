<?php

namespace App\Http\Controllers;

use App\User;
use Gate;

class userInformationController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index()
    {
        return view('UserInformation');
    }


    public function show($id)
    {
        if($user = User::all()->find($id))
        {
            return response()->json($user);
        }
        else
        {
            return response()->json($id);
        }
    }
}
