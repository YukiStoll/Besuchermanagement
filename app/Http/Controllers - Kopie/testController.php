<?php

namespace App\Http\Controllers;

use Gate;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
        $data = [
            'module' => 'mawaCommonImport',
            'requestFormat' => 'json',
            'action' => 'create',
            'auth' => [
                'user' => 'mawa',
                'password' => 'awam',
            ],
            'params' => [
                'object' => 'badge',
                'badgeno' => '0022803',
                'badgeCoding' => '0022803',
                'active' => 'true',
            ],
        ];
        $data = json_decode('{"module":"mawaCommonImport","requestFormat":"json","action":"create","auth":{"user":"mawa","password":"awam"},"params":{"object":"badge","badgeno":"0022803","badgeCoding":"0022803","active":"true"}}');
        $client = new Client();
        $request = $client->request('POST' ,env('MaWaURL'),[ 'json' => [
            'module' => 'mawaCommonImport',
            'requestFormat' => 'json',
            'action' => 'create',
            'auth' => [
                'user' => 'mawa',
                'password' => 'awam',
            ],
            'params' => [
                'object' => 'badge',
                'badgeno' => '0022803',
                'badgeCoding' => '0022803',
                'active' => 'true',
            ],
        ] ]);
        $response = $request->getBody();
        return response()->json([
            'Test' => '1',
            'Der' => '2',
            'response' => $response
        ]);
    }
}
