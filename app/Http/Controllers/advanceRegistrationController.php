<?php

namespace App\Http\Controllers;


use App\advanceRegistration;
use App\Mail\PermissionNotice;
use Gate;
use App\admin_setting;
use App\areaPermission;
use App\history_action_log;
use App\User;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdvanceRegistrationController extends UNOController
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        $date = date('Y-m-d', strtotime(now()));
        $admin_settings = admin_setting::all()->where("setting_key", "=", "room_occupancy_file")->first();
        $usersOnHoliday = DB::table("holiday_allocation")->select("userID")
        ->where(function ($query) use ($date)
        {
            $query->where([
                ["from", "<=", "{$date}"],
                ["to", ">=", "{$date}"],
            ]);
        })
        ->pluck("userID");
        $entryUsers = User::select('users.id', 'forename', 'surname')
            ->where('canIssueEntryPermit', '=', '1')
            ->whereNotIn("id", $usersOnHoliday)
            ->orderBy('surname', 'asc')
            ->get();

        $workUsers = User::select('users.id', 'forename', 'surname')
            ->whereNotIn("id", $usersOnHoliday)
            ->where('canIssueWorkPermit', '=', '1')
            ->orderBy('surname', 'asc')
            ->get();

        $areaPermissions = areaPermission::select('id', 'name')
        ->orderBy('name', 'asc')
        ->get();

        return $this->test(
            view('advanceRegistration')
            ->with("admin_settings", $admin_settings)
            ->with('entryUsers', $entryUsers)
            ->with('workUsers', $workUsers)
            ->with("areaPermissions", $areaPermissions)
        );
    }
    public function entryPermission($id, Request $request)
    {
        $request->validate([
            'status' => 'required',
        ]);
        $advanceRegistration = advanceRegistration::all()->where("visitId","=", $id)->first();
        if($advanceRegistration)
        {
            if($advanceRegistration->startDate < now())
            {
                return redirect()->route('home')->with('successEntryPermission', 4);
            }
            $advanceRegistration->entrypermission = $request->status;
            $advanceRegistration->save();
        }
        if($request->status == "granted")
        {
            history_action_log::insert(["userID" => Auth::user()->id, "action" => "entry_permission_granted", "forProcessID" => $id]);
        }
        else
        {
            history_action_log::insert(["userID" => Auth::user()->id, "action" => "entry_permission_denied", "forProcessID" => $id]);
        }
        if($advanceRegistration)
        {
            $employee = DB::table('users')
                ->select('name', 'department', 'email', 'telephone_number', 'mobile_number')
                ->where('id', '=', $advanceRegistration->userId)
                ->first();

            $data['employee_name'] = $employee->name;
            $userids = DB::table("userallocation")->select("userID")->where("allocationID", $advanceRegistration->allocationid)->pluck("userID");
            if(isset($userids) && count($userids) > 1)
            {
                $data['employee_name'] = "";
                $employees = User::select("forename", "surname")->whereIn("id", $userids)->where("id", "!=", $advanceRegistration->userId)->get();
                if(count($employees) > 1)
                {
                    $count = 0;
                    foreach($employees as $tempEmployee)
                    {
                        if($count != 0)
                        {
                            $data['employee_name'] = $tempEmployee->forename . " " . $tempEmployee->surname . ", " . $data['employee_name'];
                        }
                        else
                        {
                            $count = 1;
                            $data['employee_name'] = $tempEmployee->forename . " " . $tempEmployee->surname . $data['employee_name'];
                        }
                    }
                }
                else
                {
                    $data['employee_name'] = $employees[0]->forename . " " . $employees[0]->surname;
                }
            }

            $data['employee_department'] = $employee->department;
            $data['employee_email'] = $employee->email;
            $data['employee_telephone_number'] = $employee->telephone_number;
            $data['employee_mobile_number'] = $employee->mobile_number;
            $data['approverName'] = Auth::user()->name;
            $data['startDate'] = $advanceRegistration->startDate;
            $data['endDate'] = $advanceRegistration->endDate;
            $data['permission_type'] = "entry_permission";
            $data['reasonForVisit'] = $advanceRegistration->reasonForVisit;
            $data['workPermissionApprovalText'] = $advanceRegistration->workPermissionApprovalText;
            $data['entryPermissionText'] = $advanceRegistration->entryPermissionText;
            $visitorids[] = DB::table('visitorallocation')
                ->select('visitorid')
                ->where('allocationid', '=', $advanceRegistration->allocationid)
                ->get();
            foreach ($visitorids[0] as $id)
            {
                $names[] = DB::table('visitors')
                    ->select('forename', 'surname', 'salutation', 'title', 'language', 'id')
                    ->find($id->visitorid);
            }
            $success = 1;
            if($request->status == "granted")
            {
                $content = DB::table('summernotes')
                    ->select('content_de')
                    ->where('id','=',11)
                    ->get();
            }
            else
            {
                $content = DB::table('summernotes')
                    ->select('content_de')
                    ->where('id','=',9)
                    ->get();
                    $success = 3;
            }
            Log::info("E-Mail für {$employee->email} wird in die Queue gesetzt.");
            Mail::to($employee->email)
                ->queue(new PermissionNotice($names, $content[0]->content_de, $data));
            Log::info("E-Mail für {$employee->email} wurde in die Queue gesetzt.");

            return redirect()->route('home')->with('successEntryPermission', $success);
        }
        return redirect()->route('home')->with('successEntryPermission', 2);
    }
    public function workPermission($id, Request $request)
    {
        $request->validate([
            'status' => 'required',
        ]);
        $advanceRegistration = advanceRegistration::all()->where("visitId","=", $id)->first();
        if($advanceRegistration)
        {
            if($advanceRegistration->startDate < now())
            {
                return redirect()->route('home')->with('successWorkPermission', 4);
            }
            $advanceRegistration->workPermission = $request->status;
            $advanceRegistration->save();
        }
        if($request->status == "granted")
        {
            history_action_log::insert(["userID" => Auth::user()->id, "action" => "work_permission_granted", "forProcessID" => $id]);
        }
        else
        {
            history_action_log::insert(["userID" => Auth::user()->id, "action" => "work_permission_denied", "forProcessID" => $id]);
        }
        if($advanceRegistration)
        {
            $employee = DB::table('users')
                ->select('name', 'department', 'email', 'telephone_number', 'mobile_number')
                ->where('id', '=', $advanceRegistration->userId)
                ->first();

            $data['employee_name'] = $employee->name;
            $userids = DB::table("userallocation")->select("userID")->where("allocationID", $advanceRegistration->allocationid)->pluck("userID");
            if(isset($userids) && count($userids) > 1)
            {
                $data['employee_name'] = "";
                $employees = User::select("forename", "surname")->whereIn("id", $userids)->where("id", "!=", $advanceRegistration->userId)->get();
                if(count($employees) > 1)
                {
                    $count = 0;
                    foreach($employees as $tempEmployee)
                    {
                        if($count != 0)
                        {
                            $data['employee_name'] = $tempEmployee->forename . " " . $tempEmployee->surname . ", " . $data['employee_name'];
                        }
                        else
                        {
                            $count = 1;
                            $data['employee_name'] = $tempEmployee->forename . " " . $tempEmployee->surname . $data['employee_name'];
                        }
                    }
                }
                else
                {
                    $data['employee_name'] = $employees[0]->forename . " " . $employees[0]->surname;
                }
            }

            $data['employee_department'] = $employee->department;
            $data['employee_email'] = $employee->email;
            $data['employee_telephone_number'] = $employee->telephone_number;
            $data['employee_mobile_number'] = $employee->mobile_number;
            $data['approverName'] = Auth::user()->name;
            $data['startDate'] = $advanceRegistration->startDate;
            $data['endDate'] = $advanceRegistration->endDate;
            $data['permission_type'] = "work_permission";
            $data['reasonForVisit'] = $advanceRegistration->reasonForVisit;
            $data['workPermissionApprovalText'] = $advanceRegistration->workPermissionApprovalText;
            $data['entryPermissionText'] = $advanceRegistration->entryPermissionText;
            $visitorids[] = DB::table('visitorallocation')
                ->select('visitorid')
                ->where('allocationid', '=', $advanceRegistration->allocationid)
                ->get();
            foreach ($visitorids[0] as $id)
            {
                $names[] = DB::table('visitors')
                    ->select('forename', 'surname', 'salutation', 'title', 'language', 'id')
                    ->find($id->visitorid);
            }
            $success = 1;
            if($request->status == "granted")
            {
                $content = DB::table('summernotes')
                    ->select('content_de')
                    ->where('id','=',6)
                    ->get();
            }
            else
            {
                $content = DB::table('summernotes')
                    ->select('content_de')
                    ->where('id','=',10)
                    ->get();
                $success = 3;
            }
            Log::info("E-Mail für {$employee->email} wird in die Queue gesetzt.");
            Mail::to($employee->email)
                ->queue(new PermissionNotice($names, $content[0]->content_de, $data));
            Log::info("E-Mail für {$employee->email} wurde in die Queue gesetzt.");

            return redirect()->route('home')->with('successWorkPermission', $success);
        }
        return redirect()->route('home')->with('successWorkPermission', 2);
    }

    public function areaPermission($id, Request $request)
    {
        $request->validate([
            'status' => 'required',
            'id' => 'required',
        ]);
        $data["areaPermissionName"] = DB::table("areapermission")->select("name")->find($request->id)->name;
        $advanceRegistration = advanceRegistration::select("startDate", "endDate", "userId", "allocationid", "reasonForVisit", "workPermissionApprovalText", "entryPermissionText")->where("visitId","=", $id)->first();
        if($advanceRegistration)
        {
            if($advanceRegistration->startDate < now())
            {
                return redirect()->route('home')->with('successAreaPermission', 4);
            }
            $areaPermission = DB::table("area_permission_status_allocation")->where(["areapermissionID" => $request->id, "allocationID" => $advanceRegistration->allocationid])->update(["status" => $request->status]);
        }
        if($request->status == "granted")
        {
            history_action_log::insert(["userID" => Auth::user()->id, "action" => "area_permission_granted", "forProcessID" => $id]);
        }
        else
        {
            history_action_log::insert(["userID" => Auth::user()->id, "action" => "area_permission_denied", "forProcessID" => $id]);
        }
        if($advanceRegistration)
        {
            $employee = DB::table('users')
                ->select('name', 'department', 'email', 'telephone_number', 'mobile_number')
                ->where('id', '=', $advanceRegistration->userId)
                ->first();

            $data['employee_name'] = $employee->name;
            $userids = DB::table("userallocation")->select("userID")->where("allocationID", $advanceRegistration->allocationid)->pluck("userID");
            if(isset($userids) && count($userids) > 1)
            {
                $data['employee_name'] = "";
                $employees = User::select("forename", "surname")->whereIn("id", $userids)->where("id", "!=", $advanceRegistration->userId)->get();
                if(count($employees) > 1)
                {
                    $count = 0;
                    foreach($employees as $tempEmployee)
                    {
                        if($count != 0)
                        {
                            $data['employee_name'] = $tempEmployee->forename . " " . $tempEmployee->surname . ", " . $data['employee_name'];
                        }
                        else
                        {
                            $count = 1;
                            $data['employee_name'] = $tempEmployee->forename . " " . $tempEmployee->surname . $data['employee_name'];
                        }
                    }
                }
                else
                {
                    $data['employee_name'] = $employees[0]->forename . " " . $employees[0]->surname;
                }
            }

            $data['employee_department'] = $employee->department;
            $data['employee_email'] = $employee->email;
            $data['employee_telephone_number'] = $employee->telephone_number;
            $data['employee_mobile_number'] = $employee->mobile_number;
            $data['startDate'] = $advanceRegistration->startDate;
            $data['endDate'] = $advanceRegistration->endDate;
            $data['permission_type'] = "area_permission";
            $data['reasonForVisit'] = $advanceRegistration->reasonForVisit;
            $data['workPermissionApprovalText'] = $advanceRegistration->workPermissionApprovalText;
            $data['entryPermissionText'] = $advanceRegistration->entryPermissionText;
            $data['approverName'] = Auth::user()->name;
            $visitorids[] = DB::table('visitorallocation')
                ->join("area_permission_status_allocation", "visitorallocation.visitorid", "area_permission_status_allocation.visitorid")
                ->select('visitorallocation.visitorid')
                ->where("area_permission_status_allocation.areapermissionID", $request->id)
                ->where("area_permission_status_allocation.allocationID", $advanceRegistration->allocationid)
                ->where('visitorallocation.allocationid', '=', $advanceRegistration->allocationid)
                ->distinct()
                ->get();
            foreach ($visitorids[0] as $id)
            {
                $names[] = DB::table('visitors')
                    ->select('forename', 'surname', 'salutation', 'title', 'language', 'id')
                    ->find($id->visitorid);
            }
            $success = 1;
            if($request->status == "granted")
            {
                $content = DB::table('summernotes')
                    ->select('content_de')
                    ->where('id', '=', 14)
                    ->get();
            }
            else
            {
                $content = DB::table('summernotes')
                    ->select('content_de')
                    ->where('id', '=', 13)
                    ->get();
                $success = 3;
            }
            Log::info("E-Mail für {$employee->email} wird in die Queue gesetzt.");
            Mail::to($employee->email)
                ->queue(new PermissionNotice($names, $content[0]->content_de, $data));
            Log::info("E-Mail für {$employee->email} wurde in die Queue gesetzt.");

            return redirect()->route('home')->with('successAreaPermission', $success);
        }
        return redirect()->route('home')->with('successAreaPermission', 2);
    }


    public function userSerach(Request $request)
    {
        $query_request = $request->get('query');
        $startDate = $request->get('startDate');
        $endDate = $request->get('endDate');

        $usersOnHoliday = DB::table("holiday_allocation")->select("userID")
        ->where(function ($query) use ($startDate, $endDate)
        {
            $query->where([
                ["to", ">=", "{$startDate}"],
                ["from", "<=", "{$startDate}"],
            ])->orWhere([
                ["to", ">=", "{$endDate}"],
                ["from", "<=", "{$endDate}"],
            ]);
        })
        ->pluck("userID");
        $user = User::select(DB::raw("CONCAT(`forename`, ' ', `surname`) as term"),'surname', 'forename', 'id')
        ->whereNotIn("id", $usersOnHoliday)
        ->where(function ($query) use ($query_request)
        {
            $query_requests = explode(" ", $query_request);
            if(count($query_requests) == 2)
            {
                $query->where([
                    ["surname", "Like", "%{$query_requests[0]}%"],
                    ["forename", "Like", "%{$query_requests[1]}%"],
                ])->orWhere([
                    ["surname", "Like", "%{$query_requests[1]}%"],
                    ["forename", "Like", "%{$query_requests[0]}%"],
                ]);
            }
            else
            {
                $query->whereRaw("MATCH (surname) AGAINST ('%{$query_request}%')")
                ->orwhereRaw("MATCH (forename) AGAINST ('%{$query_request}%')")
                ->orwhere('surname', 'like', "%{$query_request}%")
                ->orwhere('forename', 'like', "%{$query_request}%");
            }

        })
        ->where("id", "!=", Auth::user()->id)
        ->whereNull('deleted_at')
        ->limit(100)
        ->orderBy('surname', 'asc')
        ->get();

        return response()->json($user);
    }

    public function mawaUserSerach(Request $request)
    {
        $query_request = $request->get('query');

        $user = User::select(DB::raw("CONCAT(`forename`, ' ', `surname`) as term"),'surname', 'forename', 'id')
        ->where(function ($query) use ($query_request)
        {
            $query_requests = explode(" ", $query_request);
            if(count($query_requests) == 2)
            {
                $query->where([
                    ["surname", "Like", "%{$query_requests[0]}%"],
                    ["forename", "Like", "%{$query_requests[1]}%"],
                ])->orWhere([
                    ["surname", "Like", "%{$query_requests[1]}%"],
                    ["forename", "Like", "%{$query_requests[0]}%"],
                ]);
            }
            else
            {
                $query->whereRaw("MATCH (surname) AGAINST ('%{$query_request}%')")
                ->orwhereRaw("MATCH (forename) AGAINST ('%{$query_request}%')")
                ->orwhere('surname', 'like', "%{$query_request}%")
                ->orwhere('forename', 'like', "%{$query_request}%");
            }

        })
        ->whereNull('deleted_at')
        ->limit(100)
        ->orderBy('surname', 'asc')
        ->get();

        return response()->json($user);
    }
}
