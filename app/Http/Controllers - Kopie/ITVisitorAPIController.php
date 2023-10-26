<?php

namespace App\Http\Controllers;

use App\Mail\AdvancedRegistrationEmployee;
use App\Mail\visitorArrivalNotice;
use App\visit;
use Illuminate\Http\Request;
use App\Visitor;
use App\advanceRegistration;
use App\history_action_log;
use App\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use function foo\func;


class ITVisitorAPIController extends Controller
{
    public function __construct()
    {
        $this->middleware('it_porter_auth');
    }

    public function index()
    {
        Log::info("==========================================");
        Log::info("Ein Test mit der API wurde durchgeführt.");
        Log::info("==========================================");
        return $this->returnMSG(false, "OK", "OK", ['appName' => env('APP_NAME'), 'version' => env('APP_VERSION')], 200);
    }

    public function show($id)
    {
        Log::info("==========================================");
        Log::info("Anfrage nach Besuch");
        $ids = $this->partVisitId($id);
        $ids['visitID'];
        $ids['visitorID'];
        $ids['tableID'];
        return $this->getVisitData($ids['visitID'], $ids['visitorID'], $ids['tableID']);
    }

    public function search(Request $request)
    {
        Log::info("==========================================");
        Log::info("Es wird nach einem Vollwärtigen Besucher gesucht.");
        $visitID = null;
        $visitorID = null;
        $tableID = null;
        $now = date("Y-m-d H:i");
        $visitor = visitor::select("id")
            ->where("forename", "=", $request['firstname'])
            ->where("surname", "=", $request['surname'])
            ->where("dateOfBirth", "=", $request['birthdate'])
            ->first();
        if($visitor)
        {
            Log::info("Der Vollwärtige Besucher konnte anhand der Daten gefunden werden.");

            $allocations = DB::table("visitorallocation")
                ->select("allocationId")
                ->where("visitorid", "=", $visitor->id)
                ->get();

            $allocationIds = null;

            foreach ($allocations as $allocation)
            {
                $allocationIds[] = $allocation->allocationId;
            }

            $advanceRegistration = advanceRegistration::select("visitId")
                ->whereIn("allocationId", $allocationIds)
                ->where("deleted_at", "=", null)
                ->where('startDate', '>=', $now)
                ->orderBy("startDate")
                ->first();

            if($advanceRegistration)
            {
                Log::info("Die voranmeldung zu dem Besuch wurde gefunden.");
                $visitID = $advanceRegistration->visitId;
                $visitorID = $visitor->id;
            }
        }
        else
        {
            Log::info("Es wird nach einem unter Besucher gesucht.");

            $visitor = DB::table("group_visitors")
                ->select("id")
                ->where("forename", "=", $request['firstname'])
                ->where("surname", "=", $request['surname'])
                ->where("dateOfBirth", "=", $request['birthdate'])
                ->first();

            if($visitor)
            {
                Log::info("Der unter Besucher konnte anhand der Daten gefunden werden.");

                $allocations = DB::table("visitorallocation")
                    ->select("allocationId")
                    ->where("visitorid", "=", $visitor->id)
                    ->get();

                $allocationIds = null;
                foreach ($allocations as $allocation)
                {
                    $allocationIds[] = $allocation->allocationId;
                }

                $advanceRegistration = advanceRegistration::select("visitId")
                    ->whereIn("allocationId", $allocationIds)
                    ->where("deleted_at", "=", null)
                    ->where('startDate', '>=', $now)
                    ->orderBy("startDate")
                    ->first();

                if($advanceRegistration)
                {
                    Log::info("Die Voranmeldung wurde gefunden.");
                    $visitID = $advanceRegistration->visitId;
                    $visitorID = $visitor->id;
                }
                if($visitID == null)
                {
                    Log::info("Es konnte keine Voranmeldung zu diesem Besucher gefunden werden.");
                    Log::info("==========================================");
                    return $this->returnMSG(true, "NO_ADVANCEREGISTRATION_FOUND", "Keine Voranmeldung für diesen Besucher vorhanden.", "", 404);
                }
            }
            else
            {
                Log::info("Der Besucher konnte nicht gefunden werden.");
                Log::info("==========================================");
                return $this->returnMSG(true, "VISITOR_NOT_FOUND", "Der Besucher konnte nicht gefunden werden.", "", 404);
            }
        }
        return $this->getVisitData($visitID, $visitorID, $tableID);
    }

    public function store(Request $request)
    {
        Log::info("==========================================");
        Log::info("Anfrage zum Erstellen eines Besuchers");
        $requestContent = json_decode($request->getContent(), true);
        $visitor = $requestContent['visitor'];
        $visitor['landlineNumber'] = $requestContent['visitor']['phone_number'];
        $visitor['mobileNumber'] = $requestContent['visitor']['mobile_number'];
        $visitor['creator'] = 29;
        $visitor['visitorCategory'] = $visitor['category'];
        if($visitor['dateOfLastSafetyInstruction'] != null && $visitor['dateOfLastSafetyInstruction'] != "")
        {
            $visitor['safetyInstruction'] = $visitor['dateOfLastSafetyInstruction'];
        }
        $visit = $requestContent['visit'];
        $visitedPerson = $requestContent['visitedPerson'];

        $allocationid = DB::table('visitorallocation')
            ->select("allocationid")->max('allocationid');
        $allocationid = $allocationid + 1;

        $visitor = visitor::create($visitor);
        Log::info("Besucher wurde erstellt.");

        $allocation = DB::table('visitorallocation')
            ->insert(
                [
                    'allocationid' => $allocationid,
                    'visitorid' => $visitor['id'],
                    'leader' => 1,
                    'visiting' => 1,
                    'canteen' => 0,
                ]
            );
        Log::info("Eintrag in der zuordnungstabelle wurde erstellt.");
        $user = null;
        $user = DB::table('users')
            ->select("id")
            ->where(function ($query) use ($visitedPerson)
            {
                if(isset($visitedPerson['forename']) && isset($visitedPerson['surname']) && isset($visitedPerson['email']))
                {
                    $query->where('forename','LIKE',$visitedPerson['forename'])
                        ->where('surname','LIKE',$visitedPerson['surname'])
                        ->where('email','LIKE',$visitedPerson['email']);
                }
                elseif (isset($visitedPerson['forename']) && isset($visitedPerson['surname']))
                {
                    $query->where('forename','LIKE',$visitedPerson['forename'])
                        ->where('surname','LIKE',$visitedPerson['surname']);
                }
                elseif (isset($visitedPerson['email']))
                {
                    $query->where('email','LIKE',$visitedPerson['email']);
                }
            })
            ->first();
        Log::info("Der Mitarbeiter wurde gesucht.");
        if(!$user)
        {
            Log::info("Kein Mitarbeiter gefunden. Verwende User 29.");
            $visit['userId'] = 29;
        }
        else
        {
            Log::info("Ein Mitarbeiter wurde gefunden.");
            $visit['userId'] = $user->id;
        }

        do
        {
            $visitId = random_int(1000000,99999999);
            $visitidexists = DB::table('advance_registrations')
                ->where('visitId','=', $visitId)
                ->first();
        } while(!empty($visitidexists));
        Log::info("VisitId erstellt.");
        $visit['visitorallocationid'] = $allocationid;
        $visit['visitId'] = $visitId;
        $visit['isgroup'] = 0;
        $visit['leader'] = 1;
        $visitIsOk = visit::create($visit);

        history_action_log::insert(["userID" => Auth::user()->id, "action" => "create_visitor"]);
        if($visitIsOk)
        {
            Log::info("Der Besuch wurde erfolgreich Angelegt.");
            Log::info("==========================================");
            return $this->returnMSG(false, "OK", "OK", $this->fullVisitId($visitId,$visitor['id'],1), 201);
        }
        Log::info("Der Besuch konnte nicht Angelegt werden.");
        Log::info("==========================================");
        return $this->returnMSG(false, "ERROR", "ERROR", "Der Besuch konnte nicht Angelegt werden.", 400);
    }

    public function update(Request $request, $id)
    {
        Log::info("==========================================");
        Log::info("Besucher wird geupdated");
        Log::debug($request);
        $arrayRequest = json_decode($request->getContent(), true);
        $visitorRequest = $arrayRequest['visitor'];
        $visitorRequest['landlineNumber'] = $arrayRequest['visitor']['phone_number'];
        $visitorRequest['mobileNumber'] = $arrayRequest['visitor']['mobile_number'];
        $visitorRequest["questionsSafetyInstructions"] = json_encode($request->input("visitor.failedSafetyInstructionTestResult"));
        if(!empty($arrayRequest['visitor']['dateOfLastSafetyInstruction']))
        {
            $visitorRequest['safetyInstruction'] = $arrayRequest['visitor']['dateOfLastSafetyInstruction'];
        }

        $ids = $this->partVisitId($id);

        if($ids['tableID'] == 1)
        {
            $visitor = visitor::find($ids['visitorID']);
        }
        elseif($ids['tableID'] == 0)
        {
            $visitor = DB::table('group_visitors')->find($ids['visitorID']);
        }
        if($visitor)
        {
            Log::info("Der Besucher konnte gefunden werden.");
        }
        else
        {
            Log::info("Der Besucher konnte nicht gefunden werden.");
            Log::info("==========================================");
            return $this->returnMSG(false, "VISIT_WAS_NOT_FOUND", "Der Besuch konnte nicht gefunden werden.", $this->fullVisitId($ids['visitID'],$ids['visitorID'],$ids['tableID']), 400);
        }
        if($ids['tableID'] == 0)
        {
            Log::info("Der Besucher wurde als Gruppenmitglied erkannt.");
            $group_visitorData['forename'] = $visitorRequest['forename'];
            $group_visitorData['surname'] = $visitorRequest['surname'];
            $group_visitorData['dateofbirth'] = $visitorRequest['dateOfBirth'];
            if(isset($visitorRequest['safetyInstruction']) && $visitorRequest['safetyInstruction'] != null)
            {
                $group_visitorData['safetyInstruction'] = $visitorRequest['safetyInstruction'];
            }
            $group_visitorData['questionsSafetyInstructions'] = $visitorRequest["questionsSafetyInstructions"];
            Log::info("Der Besucher wird geupdated.");
            $visitor = DB::table('group_visitors')
                ->where('id','=', $ids['visitorID'])
                ->update($group_visitorData);
            if($visitor == 0 || $visitor == 1)
            {
                $visitor = DB::table('group_visitors')
                    ->where('id','=', $ids['visitorID'])
                    ->first();
            }
        }
        elseif($ids['tableID'] == 1)
        {
            $visitor->update($visitorRequest);
        }
        try
        {
            $pförtner = User::select("id")->where("role", "Gatekeeper")->first();
            history_action_log::insert(["userID" => $pförtner->id, "action" => "update_visitor", "forProcessID" => $ids['visitorID']]);
        }
        catch(Exception $e){}
        if($visitor)
        {
            Log::info("Der Besucher wurde erfolgreich geupdated.");
            $test = VisitAPIController::storeVisit($ids['visitID']);
            $visit = $test->getData(true);
            $arrayRequest['visit']['startDate'] = Carbon::now('Europe/Berlin');
            if(isset($visit['visit']) && $visit['visit'] != "" && $visit['visit'] != "")
            {
                $visit = $visit['visit'];
            }
            if(isset($arrayRequest['visitor']['carrier']) && $arrayRequest['visitor']['carrier'] != "" && $arrayRequest['visitor']['carrier'] != null)
            {
                $arrayRequest['visit']['carrier'] = $arrayRequest['visitor']['carrier'];
            }
            if($arrayRequest['visit']['reasonForVisit'] == "" && $arrayRequest['visit']['reasonForVisit'] == null && $visit != null && $visit != "")
            {
                $arrayRequest['visit']['reasonForVisit'] = $visit['reasonForVisit'];
            }
            if($test->status() != 400 && isset($visit['id']))
            {
                DB::table('visits')
                    ->where("id","=",$visit['id'])
                    ->update($arrayRequest['visit']);
               Log::info("Die Voranmeldung konnte zu einem Besuch umgewandelt werden.");
            }
            else if(isset($visit['id']))
            {
                DB::table('visits')
                    ->where("id","=",$visit['id'])
                    ->update($arrayRequest['visit']);
                Log::info("Die Voranmeldung konnte nicht zu einem Besuch umgewandelt werden.");
            }
            else
            {
                Log::info("Die Voranmeldung konnte nicht zu einem Besuch umgewandelt werden.");
            }

            $allocationIDTemp = DB::table('advance_registrations')
                ->select('userId', 'allocationid')
                ->where('visitId','=', $ids['visitID'])
                ->first();
            if($allocationIDTemp)
            {
                $allocationID = $allocationIDTemp->allocationid;
                $userID = $allocationIDTemp->userId;
            }
            else
            {
                $allocationIDTemp = DB::table('visits')
                    ->select('userId', 'visitorallocationid')
                    ->where('visitId','=', $ids['visitID'])
                    ->first();
                $allocationID = $allocationIDTemp->visitorallocationid;
                $userID = $allocationIDTemp->userId;
            }

            $userIds = DB::table("userallocation")->select("userID")->where("allocationID", $allocationID)->where("userID", "!=", $userID)->get();
            if(!$userIds->isEmpty())
            {
                foreach($userIds as $userId)
                {
                    $user_emails[] = DB::table('users')
                        ->select('email', 'name', 'mobile_number', 'telephone_number', 'department')
                        ->where('id','=', $userId->userID)
                        ->first();
                }
            }
            else
            {
                $user_emails[] = DB::table('users')
                    ->select('email', 'name', 'mobile_number', 'telephone_number', 'department')
                    ->where('id','=', $userID)
                    ->first();
            }
            $visitor_Ids = DB::table('visitorallocation')
                ->select('visitorid')
                ->where('allocationid','=', $allocationID)
                ->get();
            $send_email = DB::table('visitorallocation')
                ->select('visiting')
                ->where(function ($query) use ($visitor_Ids, $allocationID)
                {
                    for ($i = 0; $i < count($visitor_Ids); $i++)
                    {
                        if($i === 0)
                        {
                            $query->where(function ($lower_query) use ($visitor_Ids, $i, $allocationID)
                            {
                                $lower_query->where('visitorid', '=', $visitor_Ids[$i]->visitorid)
                                            ->where('visiting', '=', 0)
                                            ->where('allocationid', '=', $allocationID);
                            });
                        }
                        else
                        {
                            $query->OrWhere(function ($lower_query) use ($visitor_Ids, $i, $allocationID)
                            {
                                $lower_query->where('visitorid', '=', $visitor_Ids[$i]->visitorid)
                                            ->where('visiting', '=', 0)
                                            ->where('allocationid', '=', $allocationID);
                            });
                        }

                    }
                })
                ->get();
            if($user_emails != null && $send_email != [])
            {
                $visitorsIDs = DB::table("visitorallocation")->select("visitorid")->where("allocationid", $allocationID)->pluck("visitorid");
                $visitors = Visitor::select("forename", "surname")->whereIn("id", $visitorsIDs)->get();
                $contentArrivalNotice = DB::table('summernotes')->find(7);
                foreach($user_emails as $user_email)
                {
                    Log::info("E-Mail für {$user_email->email} wird in die Queue gesetzt.");
                    Mail::to($user_email->email)
                        ->queue(new visitorArrivalNotice($visitor, $ids['visitID'], $user_email, $contentArrivalNotice->content_de, $visit, $visitors));
                    Log::info("E-Mail für {$user_email->email} wurde in die Queue gesetzt.");
                }
            }
            else
            {
                Log::info("Die E-Mail des Angestellten konnte nicht gefunden werden.");
            }
            Log::info("Der Besucher wurde Erfolgreich geupdated.");
            Log::info("==========================================");
            return $this->returnMSG(false, "OK", "OK", $this->fullVisitId($ids['visitID'],$ids['visitorID'],$ids['tableID']), 200);
        }
        else
        {
            Log::info("Der Besucher konnte nicht geupdated werden.");
            Log::info("==========================================");
            return $this->returnMSG(false, "NOT_ABLE_TO_UPDATE_VISITOR", "Der Besucher konnte nicht geupdated werden.", $this->fullVisitId($ids['visitID'],$ids['visitorID'],$ids['tableID']), 400);
        }
    }

    public function returnMSG($error, $errorKey, $errorMSG, $returnValue, $statusCode)
    {
        return response()->json([
            "meta" => [
                "error" => $error,
                "errorKey" => $errorKey,
                "errorMsg" => $errorMSG,
            ],
            "result" => $returnValue
        ], $statusCode);
    }

    public function partVisitId($id)
    {
        $ids['visitID'] = substr($id, 0, strpos($id, '-'));
        $ids['visitorID'] = substr($id, strpos($id, '-') + 1, strpos(substr($id, strpos($id, '-') + 1), '-'));
        $ids['tableID'] = substr(substr($id, strpos($id, '-') + 1), strpos(substr($id, strpos($id, '-') + 1), '-') + 1);
        return $ids;
    }

    public function fullVisitId($visitId, $visitorId, $tableId)
    {
        return $visitId . "-" . $visitorId . "-" . $tableId;
    }

    public function getVisitData($visitId, $visitorId, $tableId)
    {
        $visitor = visitor::select("visitorCategory")
            ->where("id", "=", $visitorId)
            ->first();
        if($visitor && $visitor->visitorCategory == "Lieferant")
        {
            $advanceRegistration = DB::table("visits")
                ->select("startDate", "endDate", "userId", "visitorallocationid", "carrier", "orderNumber", "cargo", "reasonForVisit", "vehicleRegistrationNumber")
                ->where('visitId','=', $visitId)
                ->where('deleted_at','=',null)
                ->first();
            $advanceRegistration->allocationid = $advanceRegistration->visitorallocationid;
        }
        else
        {
            $advanceRegistration = advanceRegistration::select("startDate", "endDate", "userId", "allocationid")
                ->where('visitId','=', $visitId)
                ->where('deleted_at','=', null)
                ->first();
            if($advanceRegistration)
            {
                $advanceRegistration->carrier = "";
                $advanceRegistration->vehicleRegistrationNumber = "";
                $advanceRegistration->orderNumber = "";
                $advanceRegistration->cargo = "";
                $advanceRegistration->reasonForVisit = "";
            }
        }
        if(!$advanceRegistration)
        {
            $advanceRegistration = DB::table("visits")
            ->select("startDate", "endDate", "userId", "visitorallocationid", "carrier", "orderNumber", "cargo", "reasonForVisit", "vehicleRegistrationNumber")
            ->where('visitId','=', $visitId)
            ->where('deleted_at','=',null)
            ->first();
            if($advanceRegistration)
            {
                $advanceRegistration->allocationid = $advanceRegistration->visitorallocationid;
            }
        }

        if($advanceRegistration)
        {
            Log::info("Voranmeldung wurde gefunden.");
            $allocationUser = DB::table('visitorallocation')
                ->where('visitorid', '=', $visitorId)
                ->where('allocationid', '=', $advanceRegistration->allocationid)
                ->first();

            if($allocationUser)
            {
                Log::info("Besucherzuordnung wurde gefunden.");
                $visitor = visitor::select("forename", "surname", "salutation", "title", "dateOfBirth", "company", "visitorCategory", "safetyInstruction", "landlineNumber", "mobileNumber")
                    ->where("id", "=", $visitorId)
                    ->first();
                if($visitor)
                {
                    DB::table('visitorallocation')
                        ->where('visitorid', '=', $visitorId)
                        ->where('allocationid', '=', $advanceRegistration->allocationid)
                        ->update([
                            'visiting' => 1,
                            ]);

                    Log::info("Besucher wurde gefunden.");
                    $users = DB::table('userallocation')->select("userID")->where('allocationID', '=', $advanceRegistration->allocationid)->where("userID", "!=", $advanceRegistration->userId)->first();
                    if($users)
                    {
                        $user = DB::table('users')
                            ->select("forename", "surname")
                            ->where('id', '=', $users->userID)
                            ->first();
                    }
                    else
                    {
                        $user = DB::table('users')
                            ->select("forename", "surname")
                            ->where('id', '=', $advanceRegistration->userId)
                            ->first();
                    }
                    if($user)
                    {
                        Log::info("Angestellter wurde gefunden.");
                        $urls = "";
                        if($allocationUser->leader == 1)
                        {
                            $urls = $this->getPDFs($visitId);
                        }

                        Log::info("Suche wurde Erfolgreich durchgeführt.");
                        Log::info("==========================================");
                        return $this->createReturnMessage($visitId, $visitorId, $tableId, $visitor, $urls, $advanceRegistration, $user);

                    }
                    else
                    {
                        Log::info("Der Angestellte wurde nicht gefunden.");
                        Log::info("==========================================");
                        return $this->returnMSG(true, "EMPLOYEE_NOT_FOUND", "Der Angestellte konnte nicht gefunden werden.", "", 404);
                    }

                }
                else
                {
                    Log::info("Während des Ladens der Besucher Daten ist ein Fehler Aufgetreten.");
                    Log::info("==========================================");
                    return $this->returnMSG(true, "USERDATA_NOT_AVAILABLE", "Während des Ladens der Besucher Daten ist ein Fehler Aufgetreten.", "", 500);
                }
            }
            else
            {
                Log::info("Der Besucher konnte nicht gefunden werden.");
                Log::info("==========================================");
                return $this->returnMSG(true, "VISITOR_NOT_FOUND", "Der Besucher konnte nicht gefunden werden.", "", 404);
            }
        }
        else
        {
            Log::info("Der Besuch konnte nicht gefunden werden.");
            Log::info("==========================================");
            return $this->returnMSG(true, "VISIT_NOT_FOUND", "Der Besuch konnte nicht gefunden werden.", "", 404);
        }
    }

    public function getPDFs($visitId)
    {
        $urls = null;
        if(file_exists("workPermissionDocuments/" . $visitId))
        {
            $PDFs = scandir("workPermissionDocuments/" . $visitId);
            foreach ($PDFs as $PDF)
            {
                if($PDF != "." && $PDF != "..")
                {
                    $urls[] = [ "url" => URL::to("/") . "/workPermissionDocuments/" . $visitId . "/" . rawurlencode($PDF)];
                }
            }
        }
        return $urls;
    }

    public function createReturnMessage($visitId, $visitorId, $tableId, $visitor, $urls, $advanceRegistration, $user)
    {
        return $this->returnMSG(false, "OK", "OK", [
            'id' => $this->fullVisitId($visitId, $visitorId, $tableId),
            "visitor" =>
                [
                    'category' => $visitor->visitorCategory,
                    'salutation' => $visitor->salutation,
                    'title' => $visitor->title,
                    'forename' => $visitor->forename,
                    'surname' => $visitor->surname,
                    'dateOfBirth' => $visitor->dateOfBirth,
                    'dateOfLastSafetyInstruction' => $visitor->safetyInstruction,
                    'company' => $visitor->company,
                    'carrier' => $advanceRegistration->carrier,
                    'phone_number' => $visitor->landlineNumber,
                    'mobile_number' => $visitor->mobileNumber,

                ],
            "visit" =>
                [
                    'workPermissionDocuments' => $urls,
                    'startDate' => $advanceRegistration->startDate,
                    'endDate' => $advanceRegistration->endDate,
                    'orderNumber' => $advanceRegistration->orderNumber,
                    'cargo' => $advanceRegistration->cargo,
                    'vehicleRegistrationNumber' => $advanceRegistration->vehicleRegistrationNumber,
                    'reasonForVisit' => $advanceRegistration->reasonForVisit,
                ],
            "visitedPerson" =>
                [
                    "forename" => $user->forename,
                    "surname" => $user->surname
                ],
        ], 200);
    }
}
