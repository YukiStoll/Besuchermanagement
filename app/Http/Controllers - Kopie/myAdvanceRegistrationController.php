<?php

namespace App\Http\Controllers;

use App\advanceRegistration;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class myAdvanceRegistrationController extends UNOController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function search(Request $request)
    {
        $userid = Auth::id();
        $requestData = [
            'startDate' => '',
            'endDate' => '',
            'search' => $request['search'],
            'visitIDsearch' => $request['visitIDsearch'],
            'makeVisit' => false,
            'success' => $request['success'],
            'myAdvanceRegistration' => $request['myAdvanceRegistration'],
            'hasSearchCondition' => $request['von'] ? "true" : ($request['bis'] ? "true" : ($request['search'] ? "true" : ($request['visitIDsearch'] ? "true" : "false"))),
        ];

        if(!empty($request['von']))
        {
            $requestData['startDate'] = date('Y-m-d', strtotime($request['von']));
        }
        if (!empty($request['bis']))
        {
            $requestData['endDate'] = date('Y-m-d', strtotime($request['bis']));
        }

        if(isset($request['items']))
        {
            $items = ceil($request['items']);
            if($items < 1)
            {
                $items = 5;
            }
        }
        else
        {
            $items = 10;
        }
        if(!empty($request['visitIDsearch']) && (Auth::user()->role == "Gatekeeper" || Auth::user()->role == "Admin" || Auth::user()->role == "Super Admin"))
        {
            $data = advanceRegistration::join('users','advance_registrations.userId','=','users.id')
            ->join('visitorallocation','advance_registrations.allocationid','=','visitorallocation.allocationid')
            ->join('visitors', 'visitorallocation.visitorid', '=', 'visitors.id')
            ->select
            (
                DB::Raw
                ("
                    party, advance_registrations.id, startDate, endDate, visitors.company,
                    Concat(Concat(visitors.forename, ' '),visitors.surname) AS Visitor,
                    visitors.visitorCategory, visitors.visitorDetail AS visitorDetail, users.name, visitId,
                    advance_registrations.userId, entrypermission, workPermission,
                     advance_registrations.created_at, advance_registrations.updated_at
                ")
            )
            ->where( function ($query) use ($request){
                if($request['myAdvanceRegistration'])
                {
                    $query->where('userId','=', Auth::user()->id);
                }
            })
            ->whereNull('advance_registrations.deleted_at')
            ->where('visitorallocation.leader', '=', '1')
            ->where('visitId', '=', $request['visitIDsearch'])
            ->sortable(['created_at' => 'desc'])
            ->paginate($items);
            $requestData['makeVisit'] = true;
        }
        else
        {
            $data = advanceRegistration::join('users','advance_registrations.userId','=','users.id')
            ->join('visitorallocation','advance_registrations.allocationid','=','visitorallocation.allocationid')
            ->join('visitors', 'visitorallocation.visitorid', '=', 'visitors.id')
            ->select
            (
                DB::Raw
                ("
                    party, advance_registrations.id, startDate, endDate, visitors.company AS Company,
                    Concat(Concat(visitors.forename, ' '),visitors.surname) AS Visitor,
                    visitors.visitorCategory AS visitorCategory, visitors.visitorDetail AS visitorDetail, users.name AS name, advance_registrations.visitId,
                    advance_registrations.userId, entrypermission, workPermission,
                    advance_registrations.created_at, advance_registrations.updated_at
                ")
            )
            ->where( function ($query) use ($request){
                if($request['myAdvanceRegistration'])
                {
                    $query->where('userId','=', Auth::user()->id);
                }
            })
            ->whereNull('advance_registrations.deleted_at')
            ->where(function ($query) use ($userid)
            {
                if(Auth::user()->role != "Gatekeeper" && Auth::user()->role != "Admin" && Auth::user()->role != "Super Admin")
                {
                    $query->where('userid', '=', $userid);
                }
            })
            ->where('visitorallocation.leader', '=', '1')
            ->where( function ($query) use ($requestData)
            {
                $query->where( function ($query) use ($requestData)
                {
                    if (!empty($requestData['startDate']) && !empty($requestData['endDate']))
                    {
                        $endDate = new Carbon($requestData['endDate']);
                        $query->whereBetween('advance_registrations.startDate', [$requestData['startDate'],$endDate])
                            ->whereBetween('advance_registrations.endDate', [$requestData['startDate'],$endDate->addDay()]);
                    }
                    elseif (!empty($requestData['startDate']) && empty($requestData['endDate']))
                    {
                        $startDate = new Carbon($requestData['startDate']);
                        $query->whereDate('advance_registrations.startDate','>=', $startDate);
                    }
                    elseif (empty($requestData['startDate']) && !empty($requestData['endDate']))
                    {
                        $endDate = new Carbon($requestData['endDate']);
                        $query->whereDate('advance_registrations.endDate','<=', $endDate);
                    }
                });
                $query->where( function ($query) use ($requestData)
                {
                    $searchArray = explode(" ", $requestData['search']);
                    foreach ($searchArray as $search)
                    {
                        if(substr($requestData['search'],0,strpos($requestData['search'], '-')))
                        {
                            $query->orWhere('visitors.forename','LIKE', '%' . $search . '%')
                                ->orWhere('visitors.surname','LIKE', '%' . $search . '%')
                                ->orWhere('visitors.company','LIKE', '%' . $search . '%')
                                ->orWhere('advance_registrations.visitId','LIKE', '%' . substr($requestData['search'],0,strpos($requestData['search'], '-')) . '%');
                        }
                        else
                        {
                            $query->orWhere('visitors.forename','LIKE', '%' . $search . '%')
                                ->orWhere('visitors.surname','LIKE', '%' . $search . '%')
                                ->orWhere('visitors.company','LIKE', '%' . $search . '%')
                                ->orWhere('advance_registrations.visitId','LIKE', '%' . $requestData['search'] . '%');
                        }
                    }
                });
            })
            ->sortable(['created_at' => 'desc'])
            ->paginate($items);
        }

        foreach ($data as $dat)
        {
            $dat->updated_at = Carbon::createFromFormat('Y-m-d H:i:s', $dat->updated_at, 'UTC')->setTimezone("Europe/Berlin");
            $dat->created_at = Carbon::createFromFormat('Y-m-d H:i:s', $dat->created_at, 'UTC')->setTimezone("Europe/Berlin");
        }

        $date = date('Y-m-d', strtotime(now()));
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

        return $this->test(view('myAdvanceRegistration')
            ->with('data', $data)
            ->with('pagitems', $items)
            ->with('requestData', $requestData)
            ->with('myAdvanceRegistration', $request['myAdvanceRegistration'])
            ->with('entryUsers', $entryUsers)
            ->with('workUsers', $workUsers));
    }


    public function getVisitors(Request $request)
    {
        $mainVisitors = null;
        $groupVisitors = null;
        $leaderCompany = "";
        $allocations = DB::table("visitorallocation")
            ->select("allocationid", "visitorid", "canteen", "visiting", "leader")
            ->where("allocationid", "=", $request['allocationid'])
            ->get();
        foreach ($allocations as $allocation)
        {
            $mainVisitor = DB::table("visitors")
                ->join('visitorallocation', "visitors.id",'=','visitorallocation.visitorid')
                ->join('advance_registrations', "advance_registrations.allocationid",'=','visitorallocation.allocationid')
                ->select("visitors.forename", "visitors.surname", "visitors.id", "visitors.company", "visitorallocation.canteen", "advance_registrations.visitId")
                ->where("visitors.id", "=", $allocation->visitorid)
                ->where("visitorallocation.allocationid", "=", $request['allocationid'])
                ->get();
            $mainVisitors[] = $mainVisitor;
        }
       return response()->json(
           [
                "mainVisitors" => $mainVisitors,
                "groupVisitors" => $groupVisitors,
           ]);
    }

    public function getUsers(Request $request)
    {
        $users = null;
        $allocations = DB::table("userallocation")
            ->select("allocationid", "userID")
            ->where("allocationid", "=", $request['allocationid'])
            ->get();
        $mainUser = advanceRegistration::select("userId")
            ->where("allocationid", "=", $request['allocationid'])
            ->first();
        foreach ($allocations as $allocation)
        {
            if($mainUser->userId != $allocation->userID)
            {
                $user = User::select("forename", "surname", "users.id")
                ->where("users.id", "=", $allocation->userID)
                ->get();
                $users[] = $user;
            }
        }
       return response()->json(
           [
                "users" => $users
           ]);
    }

}
