<?php

namespace App\Http\Controllers;

use Gate;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Exceptions\GuzzleRequestException;
use GuzzleHttp\Exception\RequestException;

class testController extends Controller
{

    public function __construct()
    {
        //$this->middleware('auth');
    }


    public function index(Request $request)
    {
        return view('test');
    }

    public function test(Request $request)
    {
        $mawaTest = new MaWaAPIController();
        $test = $mawaTest->upsertMawaPersons($mawaTest->getAllMaWaVisitors());
        return response()->json([
            'Test' => $test
        ]);
    }
}
