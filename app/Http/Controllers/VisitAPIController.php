<?php

namespace App\Http\Controllers;

use App\visit;
use App\advanceRegistration;
use App\history_action_log;
use App\visitor;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class VisitAPIController extends Controller
{
    public function __construct()
    {

    }
    public function index()
    {
        $visits = visit::all();
        return response()->json($visits);
    }
    public function create(Request $request)
    {

    }
    public function store(Request $request)
    {
        $this->storeVisit($request['id']);
    }
    public static function storeVisit($id)
    {
        $advanceRegistration = advanceRegistration::all()->where("visitId","=",$id)->first();
        if(!$advanceRegistration)
        {
            $advanceRegistration = advanceRegistration::all()->find($id);
        }
        $exists = visit::all()->where('advanceRegistrationId','=', $id)->first();
        if(!$exists)
        {
            $exists = visit::all()->where('visitId','=', $id)->first();
        }
        if($advanceRegistration || $exists)
        {
            if($advanceRegistration)
            {
                if($advanceRegistration->deleted_at == null)
                {
                    $store = [
                        'startDate' => Carbon::now('Europe/Berlin'),
                        'endDate' => $advanceRegistration->endDate,
                        'visitorallocationid' => $advanceRegistration->allocationid,
                        'userId' => $advanceRegistration->userId,
                        'visitId' => $advanceRegistration->visitId,
                        'entrypermission' => $advanceRegistration->entrypermission,
                        'workPermission' => $advanceRegistration->workPermission,
                        'canteenId' => $advanceRegistration->canteenId,
                        'parkingManagementId' => $advanceRegistration->parkingManagementId,
                        'advanceRegistrationId' => $advanceRegistration->id,
                        'isgroup' => $advanceRegistration->party,
                        'reasonForVisit' => $advanceRegistration->reasonForVisit,
                        'contactPossibility' => $advanceRegistration->contactPossibility,
                    ];
                    $advanceRegistration->deleted_at = date('Y-m-d H:i:s');
                    $authId = 29;
                    if(Auth::check())
                    {
                        $authId = Auth::user()->id;
                    }
                    $advanceRegistration->deleted_from_id = $authId;
                    if($advanceRegistration->save())
                    {
                        $visit = visit::create($store);

                        history_action_log::insert(["userID" => $authId, "action" => "created_visit", "forProcessID" => $advanceRegistration->visitId]);
                        if($visit)
                        {
                            return response()->json($visit);
                        }
                        else
                        {
                            Log::info('Besuch konnte nicht erstellt werden.');
                            return response()->json('Besuch konnte nicht erstellt werden.', 400);
                        }
                    }
                    else
                    {
                        Log::info('Voranmeldung konnte nicht gelöscht werden');
                        return response()->json('Voranmeldung konnte nicht gelöscht werden', 400);
                    }
                }
                $exists = visit::all()->where('advanceRegistrationId','=', $advanceRegistration->id)->first();
                if($exists)
                {
                    return response()->json(
                        [
                            'message' => 'Voranmeldung wurde bereits in einen besuch umgewandelt',
                            'visit' => $exists
                        ], 400);
                }
                else
                {
                    Log::info('Voranmeldung wurde gelöscht und es existiert kein Besuch.');
                    return response()->json(
                        [
                            'message' => 'Voranmeldung wurde gelöscht und es existiert kein Besuch.'
                        ], 400);
                }
            }
            else
            {
                Log::info('Keine Voranmeldung gefunden ein Besuch hierzu existiert.');
                return response()->json(
                    [
                        'message' => 'Keine Voranmeldung gefunden ein Besuch hierzu existiert.',
                        'visit' => $exists
                    ], 400);
            }
        }
        else
        {
            Log::info('voranmeldung konnte nicht gefunden werden');
            return response()->json('voranmeldung konnte nicht gefunden werden', 400);
        }
    }
    public function show($id)
    {
        $visit = visit::join('advance_registrations','visits.advanceRegistrationId','=','advance_registrations.id')->select(
            'visits.id',
            'visits.startDate',
            'visits.endDate',
            'visits.visitId',
            'visits.visitorallocationid',
            'visits.advanceRegistrationId',
            'visits.canteenId',
            'visits.contactPossibility',
            'visits.entryPermissionText',
            'visits.entrypermission',
            'visits.isgroup',
            'visits.reasonForVisit',
            'visits.userId',
            'visits.workPermission',
            'visits.workPermissionApprovalText',
            'advance_registrations.roadmap',
            'advance_registrations.hygieneRegulations',
        )->find($id);
        if($visit)
        {
            $visit->startTime = date('H:i',strtotime($visit->startDate));
            $visit->startDate = date('Y-m-d',strtotime($visit->startDate));
            $visit->endTime = date('H:i',strtotime($visit->endDate));
            $visit->endDate = date('Y-m-d',strtotime($visit->endDate));
            $visit->allocationid = $visit->visitorallocationid;

            $urls = null;
            if(file_exists("workPermissionDocuments/" . $visit->visitId))
            {
                $PDFs = scandir("workPermissionDocuments/" . $visit->visitId);
                foreach ($PDFs as $PDF)
                {
                    if($PDF != "." && $PDF != "..")
                    {
                        $urls[] =
                            [
                                "url" => URL::to("/") . "/workPermissionDocuments/" . $visit->visitId . "/" . $PDF,
                                "name" => $PDF,
                            ];
                    }
                }
            }

            $visit->workPermissionDocuments = $urls;
            return response()->json($visit);
        }
        else
        {
            return response()->json([
                'message' => 'User not found.',
                'User' => $id,
            ]);
        }
    }
    public function search($id, Request $request)
    {
        $visit = visit::all()->find($id);

        $visitorIDs = DB::table('visitorallocation')
            ->select("visitorid", "leader", "cardId")
            ->where('allocationid','=',$visit['visitorallocationid'])
            ->get();

        foreach ($visitorIDs as $key => $visitorID)
        {
            $visitors[] = visitor::select("id", "forename", "surname", "company", "visitorCategory", "questionsSafetyInstructions", "safetyInstruction")
                ->where('id','=', $visitorID->visitorid)
                ->first();
            $visitors[$key]->table_id = 1;
            $visitors[$key]->cardId = $visitorID->cardId;
            $visitors[$key]->mawaAreaIds = DB::table("area_permission_status_allocation")->select("areapermissionID", "status", "areapermission.name")
            ->join("areapermission", "area_permission_status_allocation.areapermissionID", "areapermission.id")
            ->where(["allocationID" => $visit['visitorallocationid'], "visitorid" => $visitorID->visitorid])
            ->get();
            $visitors[$key]->allocationid = $visit['visitorallocationid'];

        }
        $employee = DB::table('users')
            ->select("name", "telephone_number", "mobile_number")
            ->where('id', '=', $visit['userId'])
            ->first();

        $urls = null;
        if(file_exists("workPermissionDocuments/" . $visit->visitId))
        {
            $PDFs = scandir("workPermissionDocuments/" . $visit->visitId);
            foreach ($PDFs as $PDF)
            {
                if($PDF != "." && $PDF != ".." && $PDF != "deleted")
                {
                    $urls[] =
                        [
                            "url" => URL::to("/") . "/workPermissionDocuments/" . $visit->visitId . "/" . $PDF,
                            "name" => $PDF,
                        ];
                }
            }
        }
        return response()->json(
            [
                'startDate' => date('Y-m-d', strtotime($visit['startDate'])),
                'startTime' => date('H:i', strtotime($visit['startDate'])),
                'endDate' => date('Y-m-d', strtotime($visit['endDate'])),
                'endTime' => date('H:i', strtotime($visit['endDate'])),
                'visitors' => $visitors,
                'allocationid' => $visit['visitorallocationid'],
                'employee' => $employee,
                'visitId' => $visit['visitId'],
                'leaderMapping' => $visitorIDs,
                'contactPossibility' => $visit['contactPossibility'],
                'workPermissionDocuments' => $urls,
                'entryPermission' => $visit['entrypermission'],
                'reasonForVisit' => $visit['reasonForVisit'],
                'carrier' => $visit['carrier'],
                'orderNumber' => $visit['orderNumber'],
                'vehicleRegistrationNumber' => $visit['vehicleRegistrationNumber'],
                'cargo' => $visit['cargo'],

            ]
        );

    }
    public function edit(advanceRegistration $advanceRegistration)
    {

    }
    public function update(Request $request, $id)
    {

    }
    public function destroy($id, Request $request)
    {
        $request->validate([
            'endDate' => 'required',
            'deleted_at' => 'required',
            'deleted_from_id' => 'required',
        ]);
        Log::info("======================================================");
        Log::info("Löschprozess gestartet");
        Log::info("Besuch wird mit ID: {$id} gesucht");
        $visit = visit::all()->find($id);
        Log::info("Nach Person mit zugeordneter Karte im Besuch wird gesucht");
        $visitorWithCard = DB::table("visitorallocation")
            ->select("id")
            ->where("allocationid","=", $visit->visitorallocationid)
            ->whereNotNull("cardId")
            ->first();
        history_action_log::insert(["userID" => Auth::user()->id, "action" => "deleted_visit", "forProcessID" => $visit->visitId]);
        if(!$visitorWithCard && $visit)
        {
            Log::info("Besuch wurde gefunden");
            Log::info("Keine Kartenzuordnung gefunden");
            Log::info("Besuch wird geupdated");
            $visit->update($request->all());
            if($visit)
            {
                Log::info("Besuch wurde erfolgreich geupdated");
            }
            else
            {
                Log::info("Besuch wurde nicht geupdated");
            }
            Log::info("======================================================");
            return response()->json($visit);
        }
        else if($visit)
        {
            Log::info("Besuch wurde gefunden");
            Log::info("Ein oder Mehr Besucher haben eine Karte zugeordnet");
            Log::info("======================================================");
            return response()->json([
                "id" => $id,
                "key" => "VISITOR_HAS_CARD",
            ], 400);
        }
        else
        {
            Log::info("Besuch konnte nicht gefunden werden.");
            Log::info("======================================================");
            return response()->json([
                "id" => $id,
                "key" => "VISIT_NOT_FOUND",
            ], 400);
        }
    }
    public function getMaWaVisitor(Request $request)
    {
        /*
            visitorId:id,
            table_id:table_id,
            allocationId:allocationId,

            "transactionId":data['transactionId'],
            "firstname":data['forename'],
            "lastname":data['surname'],
            "validFrom":data['startDate'],
            "validTo":data['endDate'], <!-- YYYY-MM-DD HH:MM -->
         */
        $visitor = null;
        $visitorTableName = "visitors";
        if(false)
        {
            $visitorTableName = "group_visitors";
        }
        $visitor = DB::table($visitorTableName)
            ->select("forename", "surname", "company")
            ->where('id','=', $request['visitorId'])
            ->first();
        $visit = DB::table('visits')
            ->select("startDate", "endDate", "visitId")
            ->where("visitorallocationid","=", $request['allocationId'])
            ->first();

        if($visitor && $visit)
        {
            $date['startDate'] = date('Y-m-d H:i', strtotime($visit->startDate));
            $date['endDate'] = date('Y-m-d H:i', strtotime($visit->endDate));
            return response()->json([
                "visitor" => $visitor,
                "date" => $date,
                "transactionId" => $visit->visitId . "-" . $request['visitorId'] . "-" . $request['table_id'],
            ]);
        }
        else
        {
            return response()->json("Error", 400);
        }


    }
}
