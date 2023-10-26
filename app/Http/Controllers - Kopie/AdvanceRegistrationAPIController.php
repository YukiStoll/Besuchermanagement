<?php

namespace App\Http\Controllers;

use App\admin_setting;
use App\advanceRegistration;
use App\history_action_log;
use App\Mail\AdvancedRegistrationEntryPermission;
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
        Log::debug("===========================================================");
        Log::debug("Store AdvanceRegistrationAPIController");
        Log::debug($request);
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


        $canteen = 0;
        $canteenVisitorIds = null;
        $request['startDate'] = date('Y-m-d', strtotime($request['startDate'])) . " " . date('H:i', strtotime($request['startTime']));
        $request['endDate'] = date('Y-m-d', strtotime($request['endDate'])) . " " . date('H:i', strtotime($request['endTime']));
        $allocationid = DB::table('visitorallocation')
            ->select("allocationid")->max('allocationid');
        $allocationid = $allocationid + 1;
        Log::debug('foreach ($request->all(visitorids) as $visitor) zeile 86');
        foreach ($request->all('visitorids') as $visitor)
        {
            Log::debug('1');
            foreach ($visitor as $visitorids)
            {
                Log::debug('2');
                if($request->all('canteenIds')['canteenIds'] != null)
                {
                    Log::debug('3');
                    foreach ($request->all('canteenIds') as $ids)
                    {
                        Log::debug('4');
                        $canteenVisitorIds = $ids;
                        foreach ($ids as $id)
                        {
                            Log::debug('5');
                            if($id == $visitorids)
                            {
                                Log::debug('6');
                                $canteen = 1;
                                break;
                            }
                            else
                            {
                                Log::debug('7');
                                $canteen = 0;
                            }
                        }
                    }
                }
                if ($visitorids != reset($visitor) || $request->all('groupMemberForename')['groupMemberForename'] != null)
                {
                    Log::debug('8');
                    $request['party'] = 1;
                }
                else
                {
                    Log::debug('9');
                    $request['party'] = 0;
                }

                if ($visitorids == reset($visitor))
                {
                    Log::debug('10');
                    Log::debug("nur einmal nacheinander.");
                    $leaderID = $visitorids;
                    $leader = true;
                }
                else
                {
                    Log::debug('11');
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
                    Log::debug('12');

                $emails[] = DB::table('visitors')
                    ->select('email', 'surname', 'forename')
                    ->find($visitorids);
                $names[] = DB::table('visitors')
                    ->select('forename', 'surname', 'salutation', 'title', 'language', 'id')
                    ->find($visitorids);
            }
        }

        Log::debug('foreach ($request->all(userids) as $user) zeile 161');
        foreach ($request->all('userids') as $user)
        {
            Log::debug('1');
            foreach ($user as $userids)
            {
                Log::debug('2');
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
            Log::debug('visit Id erstellen');
            $request['visitId'] = random_int(10000000,99999999);
            $visitidexists = DB::table('advance_registrations')->where('visitId','=', $request['visitId'])->first();
        } while(!empty($visitidexists));

        Log::debug('canteenIDS');
        if($request->all('canteenIds')['canteenIds'] != null)
        {
            Log::debug('1');
            foreach ($request->all('canteenIds') as $canteenIds)
            {
                Log::debug('2');
                foreach ($canteenIds as $canteenId)
                {
                    Log::debug('3');
                    DB::table('canteen_allocation')
                        ->insert(["visitId" => $request['visitId'], "visitorId" => $canteenId]);
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
        Log::debug('vor dem erstellen');
        $advanceRegistration = advanceRegistration::create($request->all());
        Log::debug('nach dem erstellen');
        if($advanceRegistration)
        {
            foreach ($emails as $key=>$email)
            {
                $this->createICS($request['employee'], $request['startDate'], $request['endDate']);
                if($names[$key]->language  == "german")
                {
                    Log::debug($requests);
                    Log::debug($requests['employee']);
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

        Log::debug("===========================================================");
        Log::debug("Update AdvanceRegistrationAPIController");

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

        Log::debug('$request->all(visitorids) as $item');
        Log::debug('1');
        foreach ($request->all('visitorids') as $item)
        {
            Log::debug('1');
            $newLeader = $item[0];
            $visitorIds = $item;
        }
        foreach($allocations as $item)
        {
            Log::debug('2');
            $visitIds[] = $item->visitorid;
            if($item->leader == 1)
            {
                Log::debug('3');
                $leaderId = $item->visitorid;
            }
        }

        $oldUserIDs = DB::table("userallocation")
        ->select("userID")
        ->where("allocationID", "=", (int)$request['allocationid'])
        ->get()
        ->toArray();
        Log::debug('4');
        foreach($oldUserIDs as $userID)
        {
            Log::debug('5');
            $oldUsers[] = $userID->userID;
        }
        $removedUsers = array_diff($oldUsers, $request['userids']);
        $addedUsers = array_diff($request['userids'], $oldUsers);
        Log::debug('removedUsers');
        Log::debug($removedUsers);
        Log::debug('addedUsers');
        Log::debug($addedUsers);
        foreach($removedUsers as $remove)
        {
            Log::debug('1');
            DB::table('userallocation')
                ->where('userID', "=", $remove)
                ->where('allocationID', "=", (int)$request['allocationid'])
                ->delete();
        }
        foreach($addedUsers as $add)
        {
            Log::debug('2');
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

        Log::debug("request:");
        Log::debug($request);
        Log::debug("request['visitorids']:");
        Log::debug($request['visitorids']);
        Log::debug("oldVisitors:");
        Log::debug($oldVisitors);
        Log::debug("allocationIds:");
        Log::debug($allocationIds);
        Log::debug("array_diff(request['visitorids'], oldVisitors): (addedVisitors)");
        Log::debug(array_diff($request['visitorids'], $oldVisitors));
        Log::debug("request['visitorids']:");
        Log::debug($request['visitorids']);
        Log::debug("oldVisitors:");
        Log::debug($oldVisitors);
        Log::debug("array_diff(visitIds, visitorIds): (to Remove)");
        Log::debug(array_diff($visitIds, $visitorIds));
        Log::debug("visitIds:");
        Log::debug($visitIds);
        Log::debug("visitorIds:");
        Log::debug($visitorIds);
        Log::debug("array_diff(visitorIds, visitIds): (new Visitors)");
        Log::debug(array_diff($visitorIds, $visitIds));

        $addedVisitors = array_diff($request['visitorids'], $oldVisitors);
        //$visitIds ist ein abfrage anhand der allocation id über alle zugeordneten Besucher IDs
        //$visitorIds ist foreach ($request->all('visitorids') as $item)
        foreach(array_diff($visitIds, $visitorIds) as $key => $item)
        {
            $sendEMails = true;
            //wenn der Leader gelsöcht wird, wird vorher ein neuer Leader gesetzt
            if($leaderId == $item)
            {
                Log::debug('Leader wird gesetzt weil der ursprüngliche gelöscht wurde');
                Log::debug('newLeader');
                Log::debug($newLeader);
                Log::debug('allocationIds');
                Log::debug($allocationIds);
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
            Log::debug('1');
            if($newLeader == $item)
            {
                Log::debug('2');
                $isLeader = true;
            }
            else
            {
                Log::debug('3');
                $isLeader = false;
            }
            $setCanteen = 0;
            if(isset($canteenIds) && in_array($item, $canteenIds))
            {
                $setCanteen = 1;
            }
            //Important
            Log::debug('allocationIds');
            Log::debug($allocationIds);
            Log::debug('item');
            Log::debug($item);
            Log::debug('isLeader');
            Log::debug($isLeader);
            Log::debug('setCanteen');
            Log::debug($setCanteen);
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
            Log::debug('$advanceRegistration->update($request->all());');
            Log::debug('$request->all()');
            Log::debug($request->all());
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
                            Log::debug("=================================================================================================");
                            Log::debug("visitorIds");
                            Log::debug($visitorIds);
                            Log::debug("names");
                            Log::debug($names);
                            Log::debug("emails");
                            Log::debug($emails);
                            Log::debug("=================================================================================================");
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

        Log::debug('löschen der voranmeldung');
        Log::debug('request');
        Log::debug($request);

        if($advanceRegistration = advanceRegistration::all()->find($id))
        {
            Log::debug('advanceRegistration');
            Log::debug($advanceRegistration);
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
