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
        try {
            Log::debug('{"config":{}}');
            $clientCommit = new Client(['verify' => false]);
            $requestCommit = $clientCommit->request(
                'POST' ,
                env('MaWaURL') . 'deploy/startqueued/' . env('MaWaClient'),
                [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Accept' => '*/*',
                        'api-key' => env('MaWaSecret'),
                    ],
                    'body' => '{"config":{}}'
                ]);
        } catch (RequestException $e) {
            throw new GuzzleRequestException(
                $e->getMessage(),
                $e->getRequest(),
                $e->getResponse(),
                $e->getPrevious(),
                $e->getHandlerContext(),
                app(LoggerInterface::class)
            );
        }
        $req_body = json_decode($requestCommit->getBody(), true);
        Log::debug($req_body);
        return response()->json([
            'Test' => '1',
            'Der' => '2',
            'response' => $req_body
        ]);
    }
}
