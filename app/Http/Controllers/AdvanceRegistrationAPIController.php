<?php

namespace App\Http\Controllers;

use App\admin_setting;
use App\advanceRegistration;
use App\history_action_log;
use App\Mail\AdvancedRegistrationEntryPermission;
use App\Mail\AdvancedRegistrationAreaPermission;
use App\Mail\AdvancedRegistrationVisitor;
use App\Mail\AdvancedRegistrationEmployee;
use App\Mail\AdvancedRegistrationCanteen;
use App\User;
use \Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use App\areaPermission;
use App\visitor;

class AdvanceRegistrationAPIController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth');
    }

    public function index()
    {
        $visitor = advanceRegistration::all();

        return response()->json($visitor);
    }

    public function create(Request $request)
    {
    }

    public function store(Request $request)
    {

        $request->validate([
            'visitorids' => 'required',
            'userids' => 'required',
            'startTime' => 'required|date_format:H:i',
            'endTime' => 'required|date_format:H:i',
            'userId' => 'required',
            'startDate' => 'required|date|after_or_equal:today',
            'endDate' => 'required|date|after_or_equal:startDate',
            'contactPossibility' => 'required',
            'reasonForVisit' => 'required',
        ]);
        Log::info("===========================================================");
        Log::info("Store AdvanceRegistrationAPIController");
        if($request->all('groupMemberForename')['groupMemberForename'] != null)
        {
            $request->validate([
                'groupMemberForename.*' => 'required',
                'groupMemberSurname.*' => 'required',
            ]);
        }

        if($request['entrypermissionID'] != null)
        {
            $request->validate([
                'entryPermissionText' => 'required',
            ]);
            $request['entrypermission'] = "pending";
        }
        if($request['workPermissionID'] != null)
        {
            $request->validate([
                'workPermissionApprovalText' => 'required',
            ]);
            $request['workPermission'] = "pending";
        }

        /*foreach($request["visitorids"] as $visitorIds)
        {
            $request->validate([
                'areaPermissionSelect-' . $visitorIds => 'required',
            ]);
        }*/


        $canteen = 0;
        $canteenVisitorIds = null;
        $request['startDate'] = date('Y-m-d', strtotime($request['startDate'])) . " " . date('H:i', strtotime($request['startTime']));
        $request['endDate'] = date('Y-m-d', strtotime($request['endDate'])) . " " . date('H:i', strtotime($request['endTime']));
        $allocationid = DB::table('visitorallocation')
            ->select("allocationid")->max('allocationid');
        $allocationid = $allocationid + 1;
        foreach ($request->all('visitorids') as $visitor)
        {
            foreach ($visitor as $visitorids)
            {
                if($request->all('canteenIds')['canteenIds'] != null)
                {
                    foreach ($request->all('canteenIds') as $ids)
                    {
                        $canteenVisitorIds = $ids;
                        foreach ($ids as $id)
                        {
                            if($id == $visitorids)
                            {
                                $canteen = 1;
                                break;
                            }
                            else
                            {
                                $canteen = 0;
                            }
                        }
                    }
                }
                if ($visitorids != reset($visitor) || $request->all('groupMemberForename')['groupMemberForename'] != null)
                {
                    $request['party'] = 1;
                }
                else
                {
                    $request['party'] = 0;
                }

                if ($visitorids == reset($visitor))
                {
                    $leaderID = $visitorids;
                    $leader = true;
                }
                else
                {
                    $leader = false;
                }
                DB::table('visitorallocation')
                    ->insert(
                        [
                            'allocationid' => $allocationid,
                            'visitorid' => $visitorids,
                            'leader' => $leader,
                            'canteen' => $canteen,
                        ]
                    );

                $emails[] = DB::table('visitors')
                    ->select('email', 'surname', 'forename')
                    ->find($visitorids);
                $names[] = DB::table('visitors')
                    ->select('forename', 'surname', 'salutation', 'title', 'language', 'id')
                    ->find($visitorids);
            }
        }

        foreach ($request->all('userids') as $user)
        {
            foreach ($user as $userids)
            {
                DB::table('userallocation')
                    ->insert(
                        [
                            'allocationID' => $allocationid,
                            'userID' => $userids
                        ]
                    );
                $users[] = User::select('email', 'forename', 'surname', 'name', 'id')
                    ->find($userids);
            }
        }

        $contentVisitor = DB::table('summernotes')->find(1);
        $contentEmployee = DB::table('summernotes')->find(2);
        $contentCanteen = DB::table('summernotes')->find(3);
        $contentEntryPermission = DB::table('summernotes')->find(4);
        $contentWorkPermission = DB::table('summernotes')->find(5);

        do
        {
            $request['visitId'] = random_int(10000000,99999999);
            $visitidexists = DB::table('advance_registrations')->where('visitId','=', $request['visitId'])->first();
        } while(!empty($visitidexists));

        if($request->all('canteenIds')['canteenIds'] != null)
        {
            foreach ($request->all('canteenIds') as $canteenIds)
            {
                foreach ($canteenIds as $canteenId)
                {
                    DB::table('canteen_allocation')
                        ->insert(["visitId" => $request['visitId'], "visitorId" => $canteenId]);
                }
            }
        }


        foreach($request["visitorids"] as $visitorIDs)
        {
            //$areaPermissions = areaPermission::select('mawaID')->whereIn('id', $request['areaPermissionSelect-' . $visitorIDs])->pluck('mawaID');
            //$update = DB::table('visitorallocation')->where('allocationid', $allocationid)->where('visitorid', $visitorIDs)->update(['mawaAreaIds' => $request['areaPermissionSelect-' . $visitorIDs]]);

            if(!empty($request['areaPermissionSelect-' . $visitorIDs]))
            {
                foreach($request['areaPermissionSelect-' . $visitorIDs] as $areaID)
                {
                    $newUpdate = DB::table("area_permission_status_allocation")->upsert(
                        [
                            ['allocationID' => $allocationid, 'visitorid' => $visitorIDs, 'areapermissionID' => $areaID,  'status' => 'none'],
                        ],
                        ['allocationID', 'visitorid'],
                        ['status']
                    );
                }
                if(!$newUpdate)
                {
                    Log::error("Es ist ein Fehler beim zuordnen der Mawa-IDs: " . $request['areaPermissionSelect-' . $visitorIDs] . " für die Besucher-ID: " . $visitorIDs . " und der Allocation-ID: " . $allocationid . " aufgetreten.");
                }
            }
        }

        $filepaths = $request->all('wpd');
        if($filepaths['wpd'] != null && $filepaths['wpd'] != "null")
        {
            File::makeDirectory("workPermissionDocuments/" . $request['visitId']);
            foreach ($filepaths as $key => $filepath)
            {
                foreach ($filepath as $file)
                {
                    $pos = strrpos($file, "/");
                    $filename = substr($file, $pos);
                    $file = str_replace(URL::to('/'), "",$file);
                    $fileContent = file_get_contents(public_path() . $file);
                    do
                    {
                        $fileContent = str_replace("VisitId ",$request['visitId'],$fileContent);
                        $visidpos = strrpos($fileContent, "VisitId ");
                    } while(!empty($visidpos));
                    $fileChanged = file_put_contents(public_path() . $file, $fileContent);
                    if(isset($fileChanged) && !$fileChanged)
                    {
                        Log::info("In die PDF konnte die BesuchsID nicht hineingeschrieben werden.");
                    }
                    File::move(public_path() . $file, public_path() . "/workPermissionDocuments/" . $request['visitId'] . $filename);
                }
            }
        }

        $request['allocationid'] = $allocationid;
        $requests = $request->all();
        $requests['pdf'] = null;
        $advanceRegistration = advanceRegistration::create($request->all());
        if($advanceRegistration)
        {
            foreach ($emails as $key=>$email)
            {
                $this->createICS($request['employee'], $request['startDate'], $request['endDate']);
                if($names[$key]->language  == "german")
                {
                    Log::info("================================================================");
                    Log::info("E-Mail fÜr Besucher {$email->forename} {$email->surname} mit der E-mail {$email->email} zu Voranmeldung " . $request['visitId'] . " wird in die Queue gesetzt.");
                    Mail::to($email)
                        ->queue(new AdvancedRegistrationVisitor($names[$key], $contentVisitor->content_de, $names, $advanceRegistration->roadmap, $advanceRegistration->hygieneRegulations, null, $requests));
                    Log::info("E-Mail für Besucher {$email->forename} {$email->surname} mit der E-mail {$email->email} zu Voranmeldung " . $request['visitId'] . " wurde in die Queue gesetzt.");
                    Log::info("================================================================");
                }
                else
                {

                    Log::info("================================================================");
                    Log::info("E-Mail für Besucher {$email->forename} {$email->surname} mit der E-mail {$email->email} zu Voranmeldung " . $request['visitId'] . " wird in die Queue gesetzt.");
                    Mail::to($email)
                        ->queue(new AdvancedRegistrationVisitor($names[$key], $contentVisitor->content_en, $names, $advanceRegistration->roadmap, $advanceRegistration->hygieneRegulations, null, $requests));
                    Log::info("E-Mail für Besucher {$email->forename} {$email->surname} mit der E-mail {$email->email} zu Voranmeldung " . $request['visitId'] . " wurde in die Queue gesetzt.");
                    Log::info("================================================================");
                }
            }

            //request anpassen an was es wirklich braucht
            //$request["newMawaIDs"], $request["existingMawaIDs"]
            $this->sendMawaPermissionEMailIntern($allocationid, $this->getMawaIDsIntern($allocationid));

            if(!empty($request['entrypermissionID']))
            {
                $entryUserEmail = User::select('email')->find($request['entrypermissionID']);
                Log::info("================================================================");
                Log::info("E-Mail für entryPermission mit der E-Mail {$entryUserEmail->email} zu Voranmeldung " . $request['visitId'] . " wird in die Queue gesetzt.");
                Mail::to($entryUserEmail->email)
                    ->queue(new AdvancedRegistrationEntryPermission($names, $contentEntryPermission->content_de, null, $request));
                Log::info("E-Mail für entryPermission mit der E-Mail {$entryUserEmail->email} zu Voranmeldung " . $request['visitId'] . " wurde in die Queue gesetzt.");
                Log::info("================================================================");
            }
            if(!empty($request['workPermissionID']))
            {
                $workUserEmail = User::select('email')->find($request['workPermissionID']);
                Log::info("================================================================");
                Log::info("E-Mail für workPermissionApproval mit der E-Mail {$workUserEmail->email} zu Voranmeldung " . $request['visitId'] . " wird in die Queue gesetzt.");
                Mail::to($workUserEmail->email)
                    ->queue(new AdvancedRegistrationEntryPermission($names, $contentWorkPermission->content_de, null, $request));
                Log::info("E-Mail für workPermissionApproval mit der E-Mail {$workUserEmail->email} zu Voranmeldung " . $request['visitId'] . " wurde in die Queue gesetzt.");
                Log::info("================================================================");
            }

            $user_id = DB::table('advance_registrations')
            ->select('userId')
            ->where('allocationid','=', $request['allocationid'])
            ->first();

            $user = DB::table('users')
                ->select('email', 'name', 'department', 'mobile_number', 'telephone_number', 'forename', 'surname', 'id')
                ->where('id','=', $user_id->userId)
                ->first();
            if($user)
            {
                Log::info("================================================================");
                Log::info("E-Mail für Mitarbeiter {$user->name} mit der E-Mail {$user->email} zu Voranmeldung " . $request['visitId'] . " wird in die Queue gesetzt.");
                Mail::to($user->email)
                    ->queue(new AdvancedRegistrationEmployee($request['startDate'], $request['endDate'], $request['visitId'], $user->name, $names, $contentEmployee->content_de, null, $request['reasonForVisit'], $requests));
                Log::info("E-Mail für Mitarbeiter {$user->name} mit der E-Mail {$user->email} zu Voranmeldung " . $request['visitId'] . " wurde in die Queue gesetzt.");
                Log::info("================================================================");
            }

            if($canteenVisitorIds != null)
            {
                $canteenEmail = admin_setting::all()->where("setting_key", "=", "canteenEMail")->first();
                Log::info("================================================================");
                Log::info("E-Mail für Kantiene mit der E-Mail {$canteenEmail->setting_value} zu Voranmeldung " . $request['visitId'] . " wird in die Queue gesetzt.");
                Mail::to($canteenEmail->setting_value)
                    ->queue(new AdvancedRegistrationCanteen($request['startDate'], $request['endDate'], $request['visitId'], $request['employee'], $names, $contentCanteen->content_de, $canteenVisitorIds, $request['reasonForVisit'], $request));
                Log::info("E-Mail für Kantiene mit der E-Mail {$canteenEmail->setting_value} zu Voranmeldung " . $request['visitId'] . " wurde in die Queue gesetzt.");
                Log::info("================================================================");
            }
            history_action_log::insert(["userID" => Auth::user()->id, "action" => "create_advance_registration", "forProcessID" => $request['visitId']]);
            return redirect()->back()->with('success', true);
        }
        else
        {
            return redirect()->back()->with('fail', false);
        }
    }

    public function show($id)
    {
        $advanceRegistration = advanceRegistration::all()->find($id);
            if($advanceRegistration)
            {
                $advanceRegistration->startTime = date('H:i',strtotime($advanceRegistration->startDate));
                $advanceRegistration->startDate = date('Y-m-d',strtotime($advanceRegistration->startDate));
                $advanceRegistration->endTime = date('H:i',strtotime($advanceRegistration->endDate));
                $advanceRegistration->endDate = date('Y-m-d',strtotime($advanceRegistration->endDate));

                $urls = null;
                if(file_exists("workPermissionDocuments/" . $advanceRegistration->visitId))
                {
                    $PDFs = scandir("workPermissionDocuments/" . $advanceRegistration->visitId);
                    foreach ($PDFs as $PDF)
                    {
                        if($PDF != "." && $PDF != "..")
                        {
                            $urls[] =
                                [
                                    "url" => URL::to("/") . "/workPermissionDocuments/" . $advanceRegistration->visitId . "/" . $PDF,
                                    "name" => $PDF,
                                ];
                        }
                    }
                }

                $advanceRegistration->workPermissionDocuments = $urls;
                return response()->json($advanceRegistration);
            }
            else
            {
                return response()->json([
                    'message' => 'User not found.',
                    'User' => $id,
                ]);
            }


    }

    public function search(Request $request)
    {
        $request->validate(
            [
                'visitIDsearch' => 'required',
            ]
        );

        $advRegistration = DB::table('advance_registrations')
        ->select('id', 'userId','allocationid','visitId','startDate','endDate','roadmap','contactPossibility', 'reasonForVisit')
        ->where('visitId', '=', $request['visitIDsearch'])
        ->whereNull('deleted_at')
        ->first();
        if($advRegistration)
        {
            $employee = DB::table('users')->select('forename', 'surname')->find($advRegistration->userId);
            $advRegistration->startDate = date('m/d/Y H:i', strtotime($advRegistration->startDate));
            $advRegistration->endDate = date('m/d/Y H:i', strtotime($advRegistration->endDate));
            return response()->json([$advRegistration, $employee]);
        }

        return response()->json('Somthing went Wrong', 400);


    }

    public function edit(advanceRegistration $advanceRegistration)
    {
    }

    public function update(Request $request, $id)
    {
        $canteenIds = $request['canteenIds'];
        $request->validate([
            'visitorids' => 'required',
            'userids' => 'required',
            'startTime' => 'required|date_format:H:i',
            'endTime' => 'required|date_format:H:i',
            'startDate' => 'required|date|after_or_equal:today',
            'endDate' => 'required|date|after_or_equal:startDate',
            'contactPossibility' => 'required',
            'reasonForVisit' => 'required',
            'allocationid' => 'required',
        ]);

        if($request['entrypermissionID'] != null)
        {
            $request->validate([
                'entryPermissionText' => 'required',
            ]);
        }
        if($request['workPermissionID'] != null)
        {
            $request->validate([
                'workPermissionApprovalText' => 'required',
            ]);
        }

        $sendEMails = false;
        $request['startDate'] = date('Y-m-d', strtotime($request['startDate'])) . " " . date('H:i:s', strtotime($request['startTime']));
        $request['endDate'] = date('Y-m-d', strtotime($request['endDate'])) . " " . date('H:i:s', strtotime($request['endTime']));

        $allocations = DB::table("visitorallocation")
            ->select("allocationid", "visitorid", "leader")
            ->where("allocationid", "=" , (int)$request['allocationid'])
            ->get();

        // Setzt alle Canteen IDs auf 0 um sie im nächsten schritt wieder zu setzten
        DB::table('visitorallocation')
            ->where("allocationid", "=" , (int)$request['allocationid'])
            ->update(['canteen' => 0]);

        if($canteenIds != null)
        {
            foreach ($canteenIds as $canteenid)
            {
                DB::table('visitorallocation')
                    ->where("allocationid", "=" , (int)$request['allocationid'])
                    ->where("visitorid", "=" , $canteenid)
                    ->update(['canteen' => 1]);
            }
        }

        $leaderId = null;
        $newLeader = null;
        $visitorIds = null;
        $visitIds = null;
        $allocationIds = $request['allocationid'];
        $contentVisitor = DB::table('summernotes')->find(1);
        $contentEmployee = DB::table('summernotes')->find(2);

        foreach ($request->all('visitorids') as $item)
        {
            $newLeader = $item[0];
            $visitorIds = $item;
        }
        foreach($allocations as $item)
        {
            $visitIds[] = $item->visitorid;
            if($item->leader == 1)
            {
                $leaderId = $item->visitorid;
            }
        }

        $oldUserIDs = DB::table("userallocation")
        ->select("userID")
        ->where("allocationID", "=", (int)$request['allocationid'])
        ->get()
        ->toArray();
        foreach($oldUserIDs as $userID)
        {
            $oldUsers[] = $userID->userID;
        }
        $removedUsers = array_diff($oldUsers, $request['userids']);
        $addedUsers = array_diff($request['userids'], $oldUsers);
        foreach($removedUsers as $remove)
        {
            DB::table('userallocation')
                ->where('userID', "=", $remove)
                ->where('allocationID', "=", (int)$request['allocationid'])
                ->delete();
        }
        foreach($addedUsers as $add)
        {
            DB::table('userallocation')->insert([
                ['userID' => $add, 'allocationID' => (int)$request['allocationid']]
            ]);
        }

        //Abgfrage um alle breits dem Besuch zugeordneten Besucher anhand der allocation ID als Array zu erhalten.
        $oldVisitorIDs = DB::table("visitorallocation")
        ->select("visitorid")
        ->where("allocationid", "=", (int)$request['allocationid'])
        ->get()
        ->toArray();
        //Wandelt das Object in ein Array um in dem nur die Besucher ID steht um diese später mit der array_diff function vergleichen zu können.
        foreach($oldVisitorIDs as $visitorID)
        {
            $oldVisitors[] = $visitorID->visitorid;
        }
        //erhalte alle neuen Besucher die in dieser Anfrage hinzugefügt werden sollen.
        $addedVisitors[] = [];

        $addedVisitors = array_diff($request['visitorids'], $oldVisitors);
        //$visitIds ist ein abfrage anhand der allocation id über alle zugeordneten Besucher IDs
        //$visitorIds ist foreach ($request->all('visitorids') as $item)
        foreach(array_diff($visitIds, $visitorIds) as $key => $item)
        {
            $sendEMails = true;
            //wenn der Leader gelsöcht wird, wird vorher ein neuer Leader gesetzt
            if($leaderId == $item)
            {
                DB::table("visitorallocation")
                    ->where("visitorid","=", $newLeader)
                    ->where("allocationid","=", $allocationIds)
                    ->update(['leader' => 1]);
            }
            //Löschen aller array_diff_assoc($visitIds, $visitorIds)
            DB::table("visitorallocation")
                ->where("visitorid","=", $item)
                ->where("allocationid","=", $allocationIds)
                ->delete();
        }

        foreach(array_diff($visitorIds, $visitIds) as $key => $item)
        {
            foreach ($visitorIds as $visitorid)
            {
                if ($visitorid != reset($visitorIds))
                {
                    $party = 1;
                }
                else
                {
                    $party = 0;
                }
            }
            if($newLeader == $item)
            {
                $isLeader = true;
            }
            else
            {
                $isLeader = false;
            }
            $setCanteen = 0;
            if(isset($canteenIds) && in_array($item, $canteenIds))
            {
                $setCanteen = 1;
            }
            //Important
            DB::table('visitorallocation')
                ->insert(
                    [
                        'allocationid' => $allocationIds,
                        'visitorid' => $item,
                        'leader' => $isLeader,
                        'canteen' => $setCanteen,
                    ]
                );
            $sendEMails = true;
        }

        $advanceRegistration = advanceRegistration::all()->find($id);
        if($advanceRegistration)
        {
            if($request['startDate'] != $advanceRegistration->startDate || $request['endDate'] != $advanceRegistration->endDate)
            {
                $sendEMails = true;
                $tempVisitorids = DB::table("visitorallocation")
                    ->select("visitorid")
                    ->where("allocationid", "=", $allocationIds)
                    ->pluck("visitorid");
                foreach($tempVisitorids as $tempVisitorid)
                {
                    $emails[] = DB::table('visitors')
                    ->select('email', 'forename', 'surname', 'title', 'language', 'id', 'salutation')
                    ->find($tempVisitorid);
                }
            }
            if($request->hasFile('pdf'))
            {
                $files = $request->file('pdf');
                File::makeDirectory("workPermissionDocuments/" . $id);
                foreach ($files as $file)
                {
                    $fileContent = file_get_contents(public_path() . $file);
                    do
                    {
                        $fileContent = str_replace("VisitId ",$request['visitId'],$fileContent);
                        $visidpos = strrpos($fileContent, "VisitId ");
                    } while(!empty($visidpos));
                    $fileChanged = file_put_contents(public_path() . $file, $fileContent);
                    if(isset($fileChanged) && !$fileChanged)
                    {
                        Log::info("In die PDF konnte die BesuchsID nicht hineingeschrieben werden.");
                    }

                    if($file->isValid() && $file->getClientMimeType() == 'application/pdf')
                    {
                        $file->move("workPermissionDocuments/" . $id, $file->getClientOriginalName());
                    }
                }
            }

            foreach($addedVisitors as $key => $visitorId)
            {
                $emails[] = DB::table('visitors')
                    ->select('email', 'forename', 'surname', 'title', 'language', 'id', 'salutation')
                    ->find($visitorId);
            }
            foreach($visitorIds as $key => $visitorId)
            {
                $names[] = DB::table('visitors')
                    ->select('forename', 'surname', 'salutation', 'title', 'language', 'id')
                    ->find($visitorId);
            }
            $request['employee'] = DB::table('users')
                ->select('name', 'email', 'department', 'mobile_number', 'telephone_number')
                ->where('id', '=', $request['userId'])
                ->first();
            $request['visitId'] = $advanceRegistration->visitId;
            if (reset($visitorIds) != end($visitorIds))
            {
                $request['party'] = 1;
            }
            else
            {
                $request['party'] = 0;
            }

            $sendEntryEmail = false;
            if($advanceRegistration->entrypermissionID != $request['entrypermissionID'])
            {

                $request['entrypermission'] = "pending";
                $sendEntryEmail = true;
            }
            $sendWorkEmail = false;
            if($advanceRegistration->workPermissionID != $request['workPermissionID'])
            {
                $request['workPermission'] = "pending";
                $sendWorkEmail = true;
            }
            $advanceRegistration->update($request->all());
            if($advanceRegistration)
            {
                if(isset($emails))
                {
                    $emails = json_decode(json_encode($emails), true);
                    foreach ($emails as $key=>$email)
                    {
                        if($email != null && $sendEMails)
                        {
                            $this->createICS($request['employee'], $request['startDate'], $request['endDate']);
                            $email = json_decode(json_encode($email));
                            if($email->language  == "german")
                            {
                                Log::info("================================================================");
                                Log::info("E-Mail fÜr Besucher {$email->forename} {$email->surname} mit der E-mail {$email->email} zu Voranmeldung " . $request['visitId'] . " wird in die Queue gesetzt.");
                                Mail::to($email)
                                    ->queue(new AdvancedRegistrationVisitor($email, $contentVisitor->content_de, json_decode(json_encode($email), true), $advanceRegistration->roadmap, $advanceRegistration->hygieneRegulations, null, $request->all()));
                                Log::info("E-Mail fÜr Besucher {$email->forename} {$email->surname} mit der E-mail {$email->email} zu Voranmeldung " . $request['visitId'] . " wurde in die Queue gesetzt.");
                                Log::info("================================================================");
                            }
                            else
                            {
                                Log::info("================================================================");
                                Log::info("E-Mail fÜr Besucher {$email->forename} {$email->surname} mit der E-mail {$email->email} zu Voranmeldung " . $request['visitId'] . " wird in die Queue gesetzt.");
                                Mail::to($email)
                                    ->queue(new AdvancedRegistrationVisitor($email, $contentVisitor->content_en, json_decode(json_encode($email), true), $advanceRegistration->roadmap, $advanceRegistration->hygieneRegulations, null, $request->all()));
                                Log::info("E-Mail fÜr Besucher {$email->forename} {$email->surname} mit der E-mail {$email->email} zu Voranmeldung " . $request['visitId'] . " wurde in die Queue gesetzt.");
                                Log::info("================================================================");
                            }
                        }
                    }
                }

                $contentEntryPermission = DB::table('summernotes')->find(4);
                $contentWorkPermission = DB::table('summernotes')->find(5);
                if($sendEntryEmail)
                {
                    $entryUserEmail = User::select('email')->find($request['entrypermissionID']);
                    Log::info("================================================================");
                    Log::info("E-Mail für entryPermission mit der E-Mail {$entryUserEmail->email} zu Voranmeldung " . $request['visitId'] . " wird in die Queue gesetzt.");
                    Mail::to($entryUserEmail->email)
                        ->queue(new AdvancedRegistrationEntryPermission($names, $contentEntryPermission->content_de, null, $request));
                    Log::info("E-Mail für entryPermission mit der E-Mail {$entryUserEmail->email} zu Voranmeldung " . $request['visitId'] . " wurde in die Queue gesetzt.");
                    Log::info("================================================================");
                }
                if($sendWorkEmail)
                {
                    $workUserEmail = User::select('email')->find($request['workPermissionID']);
                    Log::info("================================================================");
                    Log::info("E-Mail für workPermissionApproval mit der E-Mail {$workUserEmail->email} zu Voranmeldung " . $request['visitId'] . " wird in die Queue gesetzt.");
                    Mail::to($workUserEmail->email)
                        ->queue(new AdvancedRegistrationEntryPermission($names, $contentWorkPermission->content_de, null, $request));
                    Log::info("E-Mail für workPermissionApproval mit der E-Mail {$workUserEmail->email} zu Voranmeldung " . $request['visitId'] . " wurde in die Queue gesetzt.");
                    Log::info("================================================================");
                }


                $user_id = DB::table('advance_registrations')
                ->select('userId')
                ->where('allocationid','=', $request['allocationid'])
                ->first();

                $user = DB::table('users')
                    ->select('email', 'name', 'department', 'mobile_number', 'telephone_number')
                    ->where('id','=', $user_id->userId)
                    ->first();

                if($sendEMails)
                {
                    Log::info("================================================================");
                    Log::info("E-Mail für Mitarbeiter {$user->name} mit der E-Mail {$user->email} zu Voranmeldung " . $request['visitId'] . " wird in die Queue gesetzt.");
                    Mail::to($user->email)
                        ->queue(new AdvancedRegistrationEmployee($request['startDate'], $request['endDate'], $request['visitId'], $user->name, $names, $contentEmployee->content_de, null, $request['resonForVisit'], $request));
                    Log::info("E-Mail für Mitarbeiter {$user->name} mit der E-Mail {$user->email} zu Voranmeldung " . $request['visitId'] . " wurde in die Queue gesetzt.");
                    Log::info("================================================================");
                }
                if($canteenIds != null && $sendEMails)
                {
                    $allCanteenIds = $canteenIds;
                    $contentCanteen = DB::table('summernotes')
                        ->where('id','=',3)
                        ->first();

                    $canteenEmail = admin_setting::all()->where("setting_key", "=", "canteenEMail")->first();
                    Log::info("================================================================");
                    Log::info("E-Mail für Kantiene mit der E-Mail {$canteenEmail->setting_value} zu Voranmeldung " . $request['visitId'] . " wird in die Queue gesetzt.");
                    Mail::to($canteenEmail->setting_value)
                        ->queue(new AdvancedRegistrationCanteen($request['startDate'], $request['endDate'], $request['visitId'], $request['employee'], $names, $contentCanteen->content_de, $allCanteenIds, $request['reasonForVisit'], $request, null));
                    Log::info("E-Mail für Kantiene mit der E-Mail {$canteenEmail->setting_value} zu Voranmeldung " . $request['visitId'] . " wurde in die Queue gesetzt.");
                    Log::info("================================================================");
                }
            }
            history_action_log::insert(["userID" => Auth::user()->id, "action" => "updated_advance_registration", "forProcessID" => $request['visitId']]);
            return response()->json($advanceRegistration);
        }
    }

    public function fileUpload(Request $request, $id)
    {
        $files = $request->file('pdf');
        if($files)
        {
            history_action_log::insert(["userID" => Auth::user()->id, "action" => "added_file_to_advance_registration", "forProcessID" => $request['visitId']]);
                if(!File::exists("workPermissionDocuments/" . $id))
                {
                    File::makeDirectory("workPermissionDocuments/" . $id);
                }
                foreach ($files as $file)
                {
                    if($file->isValid() && $file->getClientMimeType() == 'application/pdf')
                    {
                        $file->move("workPermissionDocuments/" . $id, $file->getClientOriginalName());
                    }
                    $file = public_path() . "//workPermissionDocuments//" . $id . "//" . $file->getClientOriginalName();
                    if(file_exists($file))
                    {
                        $fileContent = file_get_contents($file);
                        do
                        {
                            $fileContent = str_replace("VisitId ", $id, $fileContent);
                            $visidpos = strrpos($fileContent, "VisitId ");
                        } while(!empty($visidpos));
                        $fileChanged = file_put_contents($file, $fileContent);
                        if(isset($fileChanged) && !$fileChanged)
                        {
                            Log::info("In die PDF konnte die BesuchsID nicht hineingeschrieben werden.");
                        }
                    }
                }
                return response()->json(
                    [
                        "Message" => "Successfully submitted.",
                        "files" => $files,
                    ]
                );
        }
        else
        {
            return response()->json(
                [
                    "Message" => "No files submitted.",
                    "files" => $files,
                ]
            );
        }
    }

    public function fileDelete(Request $request)
    {
        history_action_log::insert(["userID" => Auth::user()->id, "action" => "deleted_file_to_advance_registration", "forProcessID" => $request['visitId']]);
        $deleted = File::delete($request->url);
        if($deleted)
        {
            return response()->json("Erfolgreich gelöscht." . $request->url);
        }
        else
        {
            return response()->json("Not deleted." . $request->url, 400);
        }
    }

    public function destroy($id, Request $request)
    {
        $request->validate([
            'deleted_at' => 'required',
            'deleted_from_id' => 'required',
        ]);


        if($advanceRegistration = advanceRegistration::all()->find($id))
        {
            history_action_log::insert(["userID" => Auth::user()->id, "action" => "deleted_advance_registration", "forProcessID" => $id]);
            $advanceRegistration->update($request->all());
            return response()->json($advanceRegistration);
        }
        else
        {
            return response()->json($id);
        }
    }

    public function tempSaveDocuments(Request $request)
    {
        $files = $request->file('pdf');
        if($files)
        {
            if(!File::exists("workPermissionDocuments/" . Auth::user()->objectguid))
            {
                File::makeDirectory("workPermissionDocuments/" . Auth::user()->objectguid);
            }
            foreach ($files as $file)
            {
                $filearr[] =
                    [
                        "name" => $file->getClientOriginalName(),
                        "url" => URL::to("/") . "/workPermissionDocuments/" . Auth::user()->objectguid . "/" . $file->getClientOriginalName(),
                    ];
                if($file->isValid() && $file->getClientMimeType() == 'application/pdf')
                {
                    $file->move("workPermissionDocuments/" . Auth::user()->objectguid, $file->getClientOriginalName());
                }
            }

            return response()->json(
                [
                    "Message" => "Successfully submitted.",
                    "files" => $filearr,
                ]
            );
        }
    }

    public function updataMawaIDForVisitor($id, Request $request)
    {
        $allocations = DB::table('area_permission_status_allocation')->select('areapermissionID')->where("visitorid", $id)->where("allocationID", $request["allocationid"])->first();
        $neueDaten = [];
        if($allocations)
        {
            $toReplace = array("[", "]", "\"");
            $allocations->areapermissionID = str_replace($toReplace, "", $allocations->areapermissionID);
            $neueDaten = array_diff($request["mawaIDs"], Explode(",", $allocations->areapermissionID));
        }
        else
        {
            $neueDaten = $request["mawaIDs"];
        }
        Log::debug($request["mawaIDs"]);
        foreach($request["mawaIDs"] as $areaId)
        {
            Log::debug("1");
            $toInsert = DB::table('area_permission_status_allocation')->select('areapermissionID')->where(['allocationID' => $request["allocationid"], 'visitorid' => $id, 'areapermissionID' => $areaId])->first();
            if(!$toInsert)
            {
                Log::debug("2");
                DB::table('area_permission_status_allocation')->insert(
                    ['allocationID' => $request["allocationid"],
                    'visitorid' => $id,
                    'areapermissionID' => $areaId]
                );
            }

        }
        DB::table('area_permission_status_allocation')->where('allocationID', $request["allocationid"])->where('visitorid', $id)->whereNotIn("areapermissionID", $request["mawaIDs"])->delete();
        if($toInsert || !$allocations)
        {
            return response()->json(
            [
                "success" => true,
                "change" => $neueDaten,
            ]);
        }
        else if($toInsert == 0)
        {
            return response()->json(
            [
                "success" => true,
                "change" => null,
            ]);
        }

        return response()->json(
            [
                "success" => false,
            ]);
    }

    public function getMawaIDs($id)
    {
        $oldIDs = DB::table('area_permission_status_allocation')->select("areapermissionID")->distinct()->where("allocationID", $id)->pluck("areapermissionID");
        $toReplace = array("[", "]", "\"");
        $newIDs = [];
        foreach($oldIDs as $id)
        {
            $replaced = str_replace($toReplace, "", $id);
            $exploded = Explode(",", $replaced);
            $arrayDiff = array_diff($exploded, $newIDs);
            $newIDs = array_merge($newIDs, $arrayDiff);
        }
        return response()->json($newIDs);
    }

    public function getMawaIDsIntern($id)
    {
        $oldIDs = DB::table('area_permission_status_allocation')->select("areapermissionID")->distinct()->where("allocationid", $id)->pluck("areapermissionID");
        $toReplace = array("[", "]", "\"");
        $newIDs = [];
        foreach($oldIDs as $id)
        {
            $replaced = str_replace($toReplace, "", $id);
            $exploded = Explode(",", $replaced);
            $arrayDiff = array_diff($exploded, $newIDs);
            $newIDs = array_merge($newIDs, $arrayDiff);
        }
        return $newIDs;
    }

    public function sendMawaPermissionEMail($id, Request $request)
    {
        if(!empty($request["existingMawaIDs"]))
        {
            $mawaIDsToSendEmailTo = array_diff($request["newMawaIDs"], $request["existingMawaIDs"]);
            return response()->json($this->sendMawaPermissionEMailIntern($id, $mawaIDsToSendEmailTo));
        }
        else
        {
            return response()->json($this->sendMawaPermissionEMailIntern($id, $request["newMawaIDs"]));
        }
    }

    public function sendMawaPermissionEMailIntern($id, $mawaIDsToSendEmailTo)
    {

        Log::debug("==================================");

        if($mawaIDsToSendEmailTo)
        {
            $contentAreaPermission = DB::table('summernotes')->find(12);
            foreach($mawaIDsToSendEmailTo as $mawaIDToSendEmailTo)
            {
                //check in allocation table if contains $mawaIDToSendEmailTo
                $visitors = visitor::select('visitors.forename', 'visitors.surname', 'visitors.salutation', 'visitors.title', 'visitors.language', 'visitors.id')
                    ->join("visitorallocation", "visitors.id", "visitorallocation.visitorid")
                    ->join("area_permission_status_allocation", "visitorallocation.visitorid", "area_permission_status_allocation.visitorid")
                    ->where("visitorallocation.allocationid", $id)
                    ->where("area_permission_status_allocation.areapermissionID",  $mawaIDToSendEmailTo)
                    ->where("area_permission_status_allocation.allocationID", $id)
                    ->distinct()
                    ->get();

                    Log::debug(visitor::select('visitors.forename', 'visitors.surname', 'visitors.salutation', 'visitors.title', 'visitors.language', 'visitors.id')
                    ->join("visitorallocation", "visitors.id", "visitorallocation.visitorid")
                    ->join("area_permission_status_allocation", "visitorallocation.visitorid", "area_permission_status_allocation.visitorid")
                    ->where("visitorallocation.allocationid", $id)
                    ->where("area_permission_status_allocation.areapermissionID",  $mawaIDToSendEmailTo)
                    ->where("area_permission_status_allocation.allocationID", $id)
                    ->distinct()->toSql());
                    Log::debug($id);
                    Log::debug($mawaIDToSendEmailTo);

                //Urlaub mit einbeziehen

                $data = advanceregistration::select("startDate", "endDate", DB::raw("CONCAT(`forename`, ' ', `surname`) as employee"), "reasonForVisit", "visitId")
                ->join("users", "advance_registrations.userId" ,"users.id")
                ->where("allocationid", $id)
                ->first();
                $data['userids'] = DB::table("userallocation")->select("userID")->where("allocationID", $id)->pluck("userID");

                $areaPermission = DB::table("areapermission")->select("name", "id")->where("id", $mawaIDToSendEmailTo)->first();
                $data['areaPermissionName'] = $areaPermission->name;
                $data['areaPermissionID'] = $areaPermission->id;

                $onHoliday = DB::table("holiday_allocation")->select("userID")
                ->where(function ($query)
                {
                    $query->where([
                        ["to", ">=", now()],
                        ["from", "<=", now()],
                    ])->orWhere([
                        ["to", ">=", now()],
                        ["from", "<=", now()],
                    ]);
                })->pluck("userID");

                for($position = 1; $position < 5; $position++)
                {
                    $areaPermissionEmail = User::select('email')
                        ->join("area_permission_allocation", "users.id", "area_permission_allocation.userID")
                        ->where("areapermissionID", $mawaIDToSendEmailTo)
                        ->where("position", $position)
                        ->whereNotIn("users.id", $onHoliday)
                        ->first();
                        if($areaPermissionEmail)
                        {
                            break;
                        }
                }

                if($areaPermissionEmail)
                {
                    Log::info("================================================================");
                    Log::info("E-Mail für Zutrittsgenehmigung mit der E-Mail {$areaPermissionEmail->email} zu Voranmeldung " . $data['visitId'] . " wird in die Queue gesetzt.");
                    Mail::to($areaPermissionEmail->email)
                        ->queue(new AdvancedRegistrationAreaPermission($visitors, $contentAreaPermission->content_de, $data));
                    Log::info("E-Mail für Zutrittsgenehmigung mit der E-Mail {$areaPermissionEmail->email} zu Voranmeldung " . $data['visitId'] . " wurde in die Queue gesetzt.");
                    Log::info("================================================================");
                }
            }

        }
        return true;
    }

    public function createICS($employee, $startDate, $endDate)
    {
        $path = public_path() . '\\' . "mails\\" . $employee . " - " . (string)date('Y-m-d H-i',strtotime($startDate)) . '.ics';
        $ical =  fopen($path, 'w');
        $eol = "\r\n";
        $icalContent =
            'BEGIN:VCALENDAR' . $eol .
            'PRODID:-//Microsoft Corporation//Outlook 16.0 MIMEDIR//EN' . $eol .
            'VERSION:2.0' . $eol .
            'METHOD:PUBLISH' . $eol .
            'X-MS-OLK-FORCEINSPECTOROPEN:TRUE' . $eol .
            'BEGIN:VTIMEZONE' . $eol .
            'TZID:W. Europe Standard Time' . $eol .
            'BEGIN:STANDARD' . $eol .
            'DTSTART:16011028T030000' . $eol .
            'RRULE:FREQ=YEARLY;BYDAY=-1SU;BYMONTH=10' . $eol .
            'TZOFFSETFROM:+0200' . $eol .
            'TZOFFSETTO:+0100' . $eol .
            'END:STANDARD' . $eol .
            'BEGIN:DAYLIGHT' . $eol .
            'DTSTART:16010325T020000' . $eol .
            'RRULE:FREQ=YEARLY;BYDAY=-1SU;BYMONTH=3' . $eol .
            'TZOFFSETFROM:+0100' . $eol .
            'TZOFFSETTO:+0200' . $eol .
            'END:DAYLIGHT' . $eol .
            'END:VTIMEZONE' . $eol .
            'BEGIN:VEVENT' . $eol .
            'CLASS:PUBLIC' . $eol .
            'CREATED:' . date('Ymd', strtotime(now())) . 'T' . date('His', strtotime(now())) . 'Z' . $eol .
            'DESCRIPTION:' . $eol .
            'DTEND;TZID="W. Europe Standard Time":' . date('Ymd', strtotime($endDate)) . 'T'.date('His', strtotime($endDate)) . $eol .
            'DTSTAMP:20200228T082950Z' . $eol .
            'DTSTART;TZID="W. Europe Standard Time":' . date('Ymd', strtotime($startDate)) . 'T'.date('His', strtotime($startDate)) . $eol .
            'LAST-MODIFIED:20200228T082950Z' . $eol .
            'LOCATION:Unilever Heppenheim' . $eol .
            'PRIORITY:5' . $eol .
            'SEQUENCE:0' . $eol .
            'SUMMARY;LANGUAGE=de:Besuch Unilever Heppenheim' . $eol .
            'TRANSP:OPAQUE' . $eol .
            'UID:040000008200E00074C5B7101A82E00800000000B0EE2CA019EED501000000000000000010000000FD9AE78CB1E839468B77A87640323BFB' . $eol .
            'X-ALT-DESC;FMTTYPE=text/html:<html xmlns:v="urn:schemas-microsoft-com:vml"xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:w="urn:schemas-microsoft-com:office:word" xmlns:m="http://schemas.microsoft.com/office/2004/12/omml" xmlns="http://www.w3.org/TR/REC-html40">PARAMETER_DESCRIPTION</html>' . $eol .
            'X-MICROSOFT-CDO-BUSYSTATUS:BUSY' . $eol .
            'X-MICROSOFT-CDO-IMPORTANCE:1' . $eol .
            'X-MICROSOFT-DISALLOW-COUNTER:FALSE' . $eol .
            'X-MS-OLK-AUTOFILLLOCATION:FALSE' . $eol .
            'X-MS-OLK-CONFTYPE:0' . $eol .
            'BEGIN:VALARM' . $eol .
            'TRIGGER:-PT15M' . $eol .
            'ACTION:DISPLAY' . $eol .
            'DESCRIPTION:Reminder' . $eol .
            'END:VALARM' . $eol .
            'END:VEVENT' . $eol .
            'END:VCALENDAR' . $eol;
        fwrite($ical, $icalContent);
        fclose($ical);
    }
}
