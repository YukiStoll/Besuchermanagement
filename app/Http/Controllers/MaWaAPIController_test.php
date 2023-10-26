<?php

namespace App\Http\Controllers;

use App\advanceRegistration;
use App\User;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MaWaAPIController extends Controller
{
    public function __construct()
    {

    }

    public function index()
    {

    }

    public function create($badge_number)
    {
        $client = new Client();
        /*
        remove !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

        return response()->json([
            'body' => "",
            'success' => true
        ]);
        */


        Log::debug('badgeno => 00' . $badge_number);
        Log::debug('badgeCoding => 1319000' . $badge_number . '0000');
        Log::debug(env('MaWaURL'));
        $request = $client->request(
            'POST' ,
            env('MaWaURL'),
            [
                'json' => [
                    'module' => 'mawaCommonImport',
                    'requestFormat' => 'json',
                    'action' => 'create',
                    'auth' => [
                        'user' => 'mawa',
                        'password' => 'awam',
                    ],
                    'params' => [
                        'object' => 'badge',
                        'badgeno' => "00" . $badge_number,
                        'badgeCoding' => "1319000" . $badge_number . "0000",
                        'active' => 'true',
                    ],
                ]
            ]);
        Log::debug("test2");
        $req_body = json_decode($request->getBody(), true);
        Log::debug("test3");
        Log::info($request->getBody());
        if($req_body['message'] == null)
        {
            return response()->json([
                'body' => $request->getBody(),
                'success' => true
            ]);
        }
        else
        {
            return response()->json([
                'body' => $request->getBody(),
                'success' => false
            ]);
        }
    }

    public function store(Request $request)
    {
        Log::info('#####################################');
        Log::info('MaWa Request Store');
        $checksum = random_int(10000000,99999999);
        DB::table('visitorallocation')
            ->where('visitorid', '=', $request['visitor']['id'])
            ->where('allocationid', '=', $request['allocationId'])
            ->update([
                'mawachecksum' => $checksum,
            ]);
        sleep(2);
        $mawachecksum = DB::table('visitorallocation')
            ->select('mawachecksum')
            ->where('visitorid', '=', $request['visitor']['id'])
            ->where('allocationid', '=', $request['allocationId'])
            ->first();
        if($mawachecksum->mawachecksum !== $checksum)
        {
            Log::info('Checksum did not match.');
            Log::info('#####################################');
            return response()->json([
                'key' => "CHECKSUM_ERROR",
                'body' => "Die Prüfsumme ist falsch, dieser Fehler tritt auf wenn versucht wird die Karten mehr wie einmal zu gleich zu erstellen.",
                'success' => false
            ]);
        }
        $allocationID = $request['allocationId'];
        $visitID = $request['visitID'];
        $visitor = $request['visitor'];
        $badge_number = "00" . $request['badge_number'];
        $dates = $request['dates'];
        if(date('Y-m-d',strtotime($dates['endDate'])) < date('Y-m-d',strtotime(now())))
        {
            Log::info('Der Besuch liegt in der Vergangenheit.');
            Log::info('#####################################');
            return response()->json([
                'key' => "THE_VISIT_IS_IN_THE_PAST",
                'body' => "Der Besuch liegt in der Vergangenheit.",
                'success' => false
            ]);
        }
        $cardExistes = DB::table('visitorallocation')
            ->where("cardId","=", $badge_number)
            ->first();
        if($cardExistes)
        {
            Log::info("Card already exists.");
            Log::info('#####################################');
            return response()->json([
                'key' => 'CARD_ALREADY_EXISTS',
                'body' => "Karte wurde bereits zugeordnet.",
                'success' => false
            ]);
        }
        else
        {
            Log::info("Card is free.");
        }
        Log::info("Trying to delete visitor with transaction ID: " . $visitID);

        /*
        remove !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

        */
        /*
        $client2 = new Client();
        $request2 = $client2->request(
            'POST' ,
            env('MaWaURL'),
            [
                'json' => [
                    "module" => "mawaCommonImport",
                    "requestFormat" => "json",
                    "action" => "delete",
                    "auth" => [
                        "user" => "mawa",
                        "password" => "awam"
                    ],
                    "params" => [
                        "object" => "visitor",
                        "transactionId" => $visitID
                    ],
                ]
            ]);
        if(json_decode($request2->getBody(), true)['retCode'] == '101')
        {
            Log::info("Deleting visitor with transaction ID: " . $visitID . " failed");
        }
        else
        {
            Log::info("Deleting visitor with transaction ID: " . $visitID . " was successful");
        }
        */
        $params = [
        "object" => "visitor",
        "transactionId" => $visitID,
        "clientId" => "P03CLNT001",
        "firstname" => $visitor['forename'],
        "lastname" => $visitor['surname'],
        "badgeno" => $badge_number,
        "validFrom" => date('Y-m-d',strtotime($dates['startDate'])) . " " . date('H:i',strtotime("00:00")),
        "validTo" => date('Y-m-d',strtotime($dates['endDate'])) . " " . date('H:i',strtotime("23:59")),
        "active" => "true",
        "groups" => ["E00"]
        ];
        Log::info("Creating visitor");
        /*
        $client = new Client();
        $request = $client->request(
            'POST' ,
            env('MaWaURL'),
            [
                'json' => [
                    'module' => 'mawaCommonImport',
                    'requestFormat' => 'json',
                    'action' => 'create',
                    'auth' => [
                        'user' => 'mawa',
                        'password' => 'awam',
                    ],
                    'params' => [
                        "object" => "visitor",
                        "transactionId" => $visitID,
                        "clientId" => "P03CLNT001",
                        "firstname" => $visitor['forename'],
                        "lastname" => $visitor['surname'],
                        "badgeno" => $badge_number,
                        "validFrom" => date('Y-m-d',strtotime($dates['startDate'])) . " " . date('H:i',strtotime("00:00")),
                        "validTo" => date('Y-m-d',strtotime($dates['endDate'])) . " " . date('H:i',strtotime("23:59")),
                        "active" => "true",
                        "groups" => ["E00"]
                    ],
                ]
            ]);
        $req_body = json_decode($request->getBody(), true);
        Log::info($request->getBody());
        $trydeleting = $req_body['retCode'];
            */
        $req_body['message'] = null;
        $trydeleting = "200";





        $counter = 0;
        if($trydeleting == "101")
        {
            Log::info("Creating visitor failed for the " . ($counter + 1) . " time.");
        }
        else
        {
            Log::info("Creating visitor was successful");
        }
        while($trydeleting == "101" && $counter < 4)
        {
            sleep(5);
            $client3 = new Client();
            $request3 = $client3->request(
                'POST' ,
                env('MaWaURL'),
                [
                    'json' => [
                        "module" => "mawaCommonImport",
                        "requestFormat" => "json",
                        "action" => "delete",
                        "auth" => [
                            "user" => "mawa",
                            "password" => "awam"
                        ],
                        "params" => [
                            "object" => "visitor",
                            "transactionId" => $visitID
                        ],
                    ]
                ]);
            $req = json_decode($request3->getBody(), true);
            $counter++;

            Log::info("!!!!!!!!!!!!!!!!!!!!!!!!!!!");
            Log::info($request3->getBody());
            Log::info("!!!!!!!!!!!!!!!!!!!!!!!!!!!");
            $client4 = new Client();
            $request4 = $client4->request(
                'POST' ,
                env('MaWaURL'),
                [
                    'json' => [
                        'module' => 'mawaCommonImport',
                        'requestFormat' => 'json',
                        'action' => 'create',
                        'auth' => [
                            'user' => 'mawa',
                            'password' => 'awam',
                        ],
                        'params' => [
                            "object" => "visitor",
                            "transactionId" => $visitID,
                            "clientId" => "P03CLNT001",
                            "firstname" => $visitor['forename'],
                            "lastname" => $visitor['surname'],
                            "badgeno" => $badge_number,
                            "validFrom" => date('Y-m-d',strtotime($dates['startDate'])) . " " . date('H:i',strtotime("00:00")),
                            "validTo" => date('Y-m-d',strtotime($dates['endDate'])) . " " . date('H:i',strtotime("23:59")),
                            "active" => "true",
                            "groups" => ["E00"]
                        ],
                    ]
                ]);
            $req_body4 = json_decode($request4->getBody(), true);
            $trydeleting = $req_body4['retCode'];
            Log::info($request4->getBody());
            if($trydeleting == "101")
            {
                Log::info("Creating visitor failed for the " . $counter . " time.");
            }
            else
            {
                Log::info("Creating visitor was successful");
            }
        }
        if($trydeleting == "101" && $counter >= 4)
        {
            Log::info('VISITOR_ALREADY_ASSIGNED_TO_A_BADGE');
            Log::info('#####################################');
            return response()->json([
                'key' => 'VISITOR_ALREADY_ASSIGNED_TO_A_BADGE',
                'body' => $req['message'],
                'success' => true
            ]);
        }
        if($req_body['message'] == null)
        {
            $valid = DB::table('visitorallocation')
                ->where('visitorid', '=', $visitor['id'])
                ->where('allocationid', '=', $allocationID)
                ->update([
                    'cardId' => $badge_number,
                ]);
            if($valid)
            {
                Log::info("Karte erfolgreich eingetragen");
            }
            else
            {
                Log::info("Karte konnte nicht eingetragen werden.");
            }
            Log::info('#####################################');
            $teleNotice = advanceRegistration::select('contactPossibility', 'userId')
            ->where('allocationid', '=', $request['allocationId'])
            ->first();
            $user = User::select('telephone_number')
            ->where('id', '=', $teleNotice->userId)
            ->first();
            return response()->json([
                'body' => $req_body['message'],
                'teleNotice' => $teleNotice->contactPossibility == "Telefon" ? true : false,
                'telephone_number' => $user->telephone_number,
                'success' => true
            ]);
        }
        else
        {
            Log::info('#####################################');
            return response()->json([
                'body' => $req_body['message'],
                'success' => false
            ]);
        }
    }

    public function destroy($badge_number, $transactionId)
    {
        $card = DB::table('visitorallocation')
            ->select('visitorid','allocationid')
            ->where("cardId","=", $badge_number)
            ->first();
        if(!$card)
        {
            return response()->json([
                'key' => 'WRONG_PERSON',
                'success' => false
            ]);
        }
        $visitId = DB::table('visits')
            ->select('visitId')
            ->where("visitorallocationid","=",$card->allocationid)
            ->first();
        $transaction_check_sum = $visitId->visitId . $card->visitorid . 1;
        if($transaction_check_sum != $transactionId)
        {
            return response()->json([
                'key' => 'WRONG_PERSON',
                'body' => $transaction_check_sum . " != " . $transactionId,
                'success' => false
            ]);
        }
        /*
        $client = new Client();
        $request = $client->request(
            'POST' ,
            env('MaWaURL'),
            [
                'json' => [
                    "module" => "mawaCommonImport",
                    "requestFormat" => "json",
                    "action" => "unset",
                    "auth" => [
                        "user" => "mawa",
                        "password" => "awam"
                    ],
                    "params" => [
                        "object" => "badge",
                        "badgeno" => "00" . $badge_number,
                    ],
                ]
            ]);
        $client2 = new Client();
        $request2 = $client2->request(
            'POST' ,
            env('MaWaURL'),
            [
                'json' => [
                    "module" => "mawaCommonImport",
                    "requestFormat" => "json",
                    "action" => "delete",
                    "auth" => [
                        "user" => "mawa",
                        "password" => "awam"
                    ],
                    "params" => [
                        "object" => "visitor",
                        "transactionId" => $transactionId
                    ],
                ]
            ]);
        $req_body = json_decode($request->getBody(), true);
        $req_body2 = json_decode($request2->getBody(), true);
        Log::info($request->getBody());
        Log::info($request2->getBody());
        */
        $req_body['message'] = null;
        $req_body2['message'] = null;
        if($req_body['message'] == null && $req_body2['message'] == null)
        {
            DB::table('visitorallocation')
                ->where('cardId', '=', $badge_number)
                ->update([
                    'cardId' => null,
                ]);
                return response()->json([
                    'body' => "",
                    'body2' => "",
                    'success' => true
                ]);
            /*return response()->json([
                'body' => $request->getBody(),
                'body2' => $request2->getBody(),
                'success' => true
            ]);*/
        }
        else
        {
            return response()->json([
                'body' => "",
                'body2' => "",
                'success' => false
            ]);
            /*return response()->json([
                'body' => $request->getBody(),
                'body2' => $request2->getBody(),
                'success' => false
            ]);*/
        }
    }
}
