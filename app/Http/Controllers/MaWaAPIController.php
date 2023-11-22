<?php

namespace App\Http\Controllers;

use App\Exceptions\GuzzleRequestException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Psr\Log\LoggerInterface;

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
        /*
        return response()->json([
            'body' => "",
            'success' => true
        ]);

        */
            try {
                $client = new Client(['verify' => false]);
                $request = $client->request(
                    'POST' ,
                    env('MaWaURL') . 'badge/insert/' . env('MaWaClient'),
                    [
                        'headers' => [
                            'Content-Type' => 'application/json',
                            'Accept' => '*/*',
                            'api-key' => env('MaWaSecret'),
                        ],
                        'body' => json_encode([
                            "config" => [
                                "persistenceStrategy" => "Commit",
                                "deploymentStrategy" => "Managed"
                            ],
                            "badges" => [[

                                    "no" => $badge_number,
                                    "status" => "Active",
                                    "scope" => "Visitor",
                                    "codingBase" => "1319000" . $badge_number . "0000",
                                    "personImportId" => null

                            ]]
                        ])
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

            try {
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





        $req_body = json_decode($request->getBody(), true);
        if(array_key_exists('errors', $req_body['badges'][0]) || $req_body['badges'][0]['errors'][0] == "Badge with this no already exists: " . $badge_number)
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
        $allocationID = $request['allocationId'];
        $visitID = $request['visitID'];
        $visitor = $request['visitor'];
        $badge_number = $request['badge_number'];
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


        $mawaIDs = DB::table('area_permission_status_allocation')
            ->join("areapermission", "area_permission_status_allocation.areapermissionID", "areapermission.id")
            ->select('mawaID')
            ->where('visitorid', '=', $request['visitor']['id'])
            ->where('allocationID', '=', $request['allocationId'])
            ->where('status', '=', "granted")
            ->pluck("mawaID");

        $mawaIDs = $mawaIDs->toArray();
        array_push($mawaIDs, env('MaWaMainKey', "E40"));
        $mawaIDArray = [];
        foreach($mawaIDs as $mawaID)
        {
            array_push($mawaIDArray, [ "id" => $mawaID]);
        }
        Log::info("Creating visitor");
        try{
            $client = new Client(['verify' => false]);
            $request = $client->request(
                'POST' ,
                env('MaWaURL') . 'person/insert/' . env('MaWaClient'),
                [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Accept' => '*/*',
                        'api-key' => env('MaWaSecret'),
                    ],
                    'body' => json_encode([
                        "persons" => [[
                                "firstName" => $visitor['forename'],
                                "lastName" => $visitor['surname'],
                                "language" => "de",
                                "companyName" => $visitor['company'],
                                "validFrom" => date('Y-m-d',strtotime($dates['startDate'])),
                                "validTo" => date('Y-m-d',strtotime($dates['endDate'])),
                                "importId" => "VISITOR-" . $visitID,
                                "status" => "Active",
                                "badges" => [[
                                        "no" => $badge_number,
                                        "scope" => "Visitor",
                                        "codingBase" => "1319000" . $badge_number . "0000",
                                        "status" => "Active"
                                ]],
                                "accessControlGroups" => $mawaIDArray
                        ]],
                        "config" => [
                            "badgeProcessStrategy" => "Default",
                            "personProcessStrategy" => "Default",
                            "personType" => "Visitor",
                            "badgeRemoveStrategy" => "DeleteBadge",
                            "processMode" => "Default",
                            "persistenceStrategy" => "Commit",
                            "deploymentStrategy" => "Managed"
                        ]
                    ])
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
        $req_body = json_decode($request->getBody(), true);
        Log::info($request->getBody());
        $trydeleting = $req_body['code'];
        $counter = 0;
        if($trydeleting != "Ok")
        {
            Log::info("Creating visitor failed for the " . ($counter + 1) . " time.");
        }
        else
        {
            Log::info("Creating visitor was successful");
        }
        while($trydeleting != "Ok" && $trydeleting != "PersistNoneWithErrors" && $counter < 4)
        {
            sleep(5);
            Log::debug(json_encode([
                "persons" => [
                    [
                        "importId" => "VISITOR-" . $visitID
                    ]
                ],
                "config" => [
                    "persistenceStrategy" => "Commit",
                    "deploymentStrategy" => "Managed",
                    "badgeRemoveStrategy" => "DeleteBadge"
                ]]));
            $client3 = new Client(['verify' => false]);
            $request3 = $client3->request(
                'POST' ,
                env('MaWaURL') . 'person/deletebulk/' . env('MaWaClient'),
                [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Accept' => '*/*',
                        'api-key' => env('MaWaSecret'),
                    ],
                    'body' => json_encode([
                        "persons" => [
                            [
                                "importId" => "VISITOR-" . $visitID
                            ]
                        ],
                        "config" => [
                            "persistenceStrategy" => "Commit",
                            "deploymentStrategy" => "Managed",
                            "badgeRemoveStrategy" => "DeleteBadge"
                        ]
                    ])
            ]);
            $req = json_decode($request3->getBody(), true);
            $counter++;

            try{
                $client4 = new Client(['verify' => false]);
                $request4 = $client4->request(
                    'POST' ,
                    env('MaWaURL') . 'person/insert/' . env('MaWaClient'),
                    [
                        'headers' => [
                            'Content-Type' => 'application/json',
                            'Accept' => '*/*',
                            'api-key' => env('MaWaSecret'),
                        ],
                        'body' => json_encode([
                            "persons" => [[
                                    "firstName" => $visitor['forename'],
                                    "lastName" => $visitor['surname'],
                                    "language" => "de",
                                    "companyName" => $visitor['company'],
                                    "validFrom" => date('Y-m-d',strtotime($dates['startDate'])),
                                    "validTo" => date('Y-m-d',strtotime($dates['endDate'])),
                                    "importId" => "VISITOR-" . $visitID,
                                    "status" => "Active",
                                    "badges" => [[
                                            "no" => $badge_number,
                                            "scope" => "Visitor",
                                            "codingBase" => "1319000" . $badge_number . "0000",
                                            "status" => "Active"
                                    ]],
                                    "accessControlGroups" => [
                                      [
                                          "id" => $mawaIDs
                                      ]
                                    ]

                            ]],
                            "config" => [
                                "badgeProcessStrategy" => "Default",
                                "personProcessStrategy" => "Default",
                                "personType" => "Visitor",
                                "badgeRemoveStrategy" => "DeleteBadge",
                                "processMode" => "Default",
                                "persistenceStrategy" => "Commit",
                                "deploymentStrategy" => "Managed"
                            ]
                        ])
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
            $req_body4 = json_decode($request4->getBody(), true);
            $trydeleting = $req_body4['code'];
            if($trydeleting != "Ok")
            {
                Log::info("Creating visitor failed for the " . $counter . " time.");
            }
            else
            {
                Log::info("Creating visitor was successful");
            }
        }
        if($trydeleting != "Ok" && $counter >= 4)
        {
            Log::info('VISITOR_ALREADY_ASSIGNED_TO_A_BADGE');
            Log::info('#####################################');
            return response()->json([
                'key' => 'VISITOR_ALREADY_ASSIGNED_TO_A_BADGE',
                'body' => $req['message'],
                'success' => true
            ]);
        }

        try {
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
        if($req_body['code'] == "Ok" || $trydeleting != "PersistNoneWithErrors")
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
            return response()->json([
                'body' => $req_body['code'],
                'success' => true
            ]);
        }
        else
        {
            Log::info('#####################################');
            return response()->json([
                'body' => $req_body['code'],
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
        try{
            $client = new Client(['verify' => false]);
            $request = $client->request(
                'POST' ,
                env('MaWaURL') . 'badge/update/' . env('MaWaClient'),
                [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Accept' => '*/*',
                        'api-key' => env('MaWaSecret'),
                    ],
                    'body' => json_encode([
                        "config" => [
                            "persistenceStrategy" => "Commit",
                            "deploymentStrategy" => "Managed"
                        ],
                        "badges" => [[
                                "no" => $badge_number,
                                "status" => "Inactive",
                                "scope" => "Visitor",
                                "codingBase" => "1319000" . $badge_number . "0000"
                        ]]
                    ])
                ]);
        } catch (RequestException $e) {
            if($e->getResponse()['code'] != "BadgeValidationError")
            {
                throw new GuzzleRequestException(
                    $e->getMessage(),
                    $e->getRequest(),
                    $e->getResponse(),
                    $e->getPrevious(),
                    $e->getHandlerContext(),
                    app(LoggerInterface::class)
                );
            }
        }
        $isOk = false;
        try{
            $client2 = new Client(['verify' => false]);
            $request2 = $client2->request(
                'POST' ,
                env('MaWaURL') . 'person/deletebulk/' . env('MaWaClient'),
                [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Accept' => '*/*',
                        'api-key' => env('MaWaSecret'),
                    ],
                    'body' => json_encode([
                        "persons" => [
                            [
                                "importId" => "VISITOR-" . $transactionId
                            ]
                        ],
                        "config" => [
                            "persistenceStrategy" => "Commit",
                            "deploymentStrategy" => "Managed",
                            "badgeRemoveStrategy" => "UnsetPerson"
                        ]
                    ])
                ]);
        } catch (RequestException $e) {
            $response = json_decode($e->getResponse()->getBody()->getContents(), true);
            if($response["code"] != "PersonValidationError")
            {
                throw new GuzzleRequestException(
                    $e->getMessage(),
                    $e->getRequest(),
                    $e->getResponse(),
                    $e->getPrevious(),
                    $e->getHandlerContext(),
                    app(LoggerInterface::class)
                );
            }
            else
            {
                $isOk = true;
            }
        }

        try {
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
        $req_body = json_decode($request->getBody(), true);
        if(!$isOk)
        {
            $req_body2 = json_decode($request2->getBody(), true);
        }
        if(($isOk && ($req_body['code'] == "Ok" || $req_body['code'] == "PersistNoneWithErrors")) || $req_body['code'] == "Ok" && $req_body2['code'] == "Ok" || $req_body2['code'] == "Error: person does not exist with this transactionId" || $req_body2['code'] == "Error: card doesnt exist.")
        {
            DB::table('visitorallocation')
                ->where('cardId', '=', $badge_number)
                ->update([
                    'cardId' => null,
                ]);
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

    public function updateDateForVisit($data)
    {
        Log::debug(json_encode([
            "persons" => [[
                "status" => "Active",
                "lastName" => $data['surname'],
                "firstName" => $data['forename'],
                "validFrom" => date('Y-m-d',strtotime($data['startDate'])),
                "validTo" => date('Y-m-d',strtotime($data['endDate'])),
                "importId" => "VISITOR-" . $data['visitID']
            ]],
            "config" => [
                "badgeProcessStrategy" => "Default",
                "personProcessStrategy" => "Default",
                "personType" => "Visitor",
                "badgeRemoveStrategy" => "DeleteBadge",
                "processMode" => "Default",
                "persistenceStrategy" => "Commit",
                "deploymentStrategy" => "Managed"
            ]
        ]));

        try{
            $client4 = new Client(['verify' => false]);
            $request4 = $client4->request(
                'POST' ,
                env('MaWaURL') . 'person/update/' . env('MaWaClient'),
                [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Accept' => '*/*',
                        'api-key' => env('MaWaSecret'),
                    ],
                    'body' => json_encode([
                        "persons" => [[
                            "status" => "Active",
                            "lastName" => $data['surname'],
                            "firstName" => $data['forename'],
                            "validFrom" => date('Y-m-d',strtotime($data['startDate'])),
                            "validTo" => date('Y-m-d',strtotime($data['endDate'])),
                            "importId" => "VISITOR-" . $data['visitID']
                        ]],
                        "config" => [
                            "badgeProcessStrategy" => "Default",
                            "personProcessStrategy" => "Default",
                            "personType" => "Visitor",
                            "badgeRemoveStrategy" => "DeleteBadge",
                            "processMode" => "Default",
                            "persistenceStrategy" => "Commit",
                            "deploymentStrategy" => "Managed"
                        ]
                    ])
                ]);

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
        }
        catch (RequestException $e)
        {
            throw new GuzzleRequestException(
                $e->getMessage(),
                $e->getRequest(),
                $e->getResponse(),
                $e->getPrevious(),
                $e->getHandlerContext(),
                app(LoggerInterface::class)
            );
            return false;
        }
        return true;
    }

}
