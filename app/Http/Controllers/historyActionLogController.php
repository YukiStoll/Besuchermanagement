<?php

namespace App\Http\Controllers;

use App\admin_setting;
use App\advanceRegistration;
use App\history_action_log;
use App\User;
use App\visit;
use App\visitor;
use Carbon\Carbon;
use DateTime;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class historyActionLogController extends UNOController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
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
        $userAction = collect(new User());
        $visitorAction = collect(new visitor());
        if(!empty($request['search']))
        {
            $userAction = User::select("id")
            ->where(function ($query) use ($request)
            {
                $searchArray = explode(" ", $request['search']);
                foreach ($searchArray as $search)
                {
                    $query->orWhere('forename','LIKE','%' . $search . '%')
                        ->orWhere('surname','LIKE','%' . $search . '%')
                        ->orWhere('email','LIKE','%' . $search . '%');
                }
            })->pluck('id');
            $visitorAction = visitor::select("id")
            ->where(function ($query) use ($request)
            {
                $searchArray = explode(" ", $request['search']);
                foreach ($searchArray as $search)
                {
                    $query->orWhere('forename','LIKE','%' . $search . '%')
                        ->orWhere('surname','LIKE','%' . $search . '%');
                }
            })->pluck('id');
        }

        $logs = history_action_log::select(DB::Raw("history_action_log.id, history_action_log.userID, history_action_log.action, history_action_log.forProcessID,
                                                     history_action_log.created_at as date, Concat(Concat(users.forename, ' '),users.surname) AS User"))
            ->join('users','history_action_log.userID','=','users.id')
            ->whereNull("history_action_log.deleted_at")
            ->where(function ($query) use ($userAction, $visitorAction, $request)
            {
                if(!$userAction->isEmpty())
                {
                    $query->whereIn("history_action_log.userID", $userAction)
                    ->orWhereIn("history_action_log.forProcessID", $userAction);
                }
                if(!$visitorAction->isEmpty())
                {
                    $query->whereIn("history_action_log.forProcessID", $visitorAction);
                }
                if(!empty($request['search']) && $visitorAction->isEmpty() && $userAction->isEmpty())
                {
                    $searchArray = explode(" ", $request['search']);
                    $query->whereIn("history_action_log.forProcessID", $searchArray);
                }
            })
            ->where('history_action_log.created_at', '>=', date('Y-m-d H:i:s', strtotime(now() . ' -6 months')))
            ->sortable(['date' => 'desc'])
            ->paginate($items);
            foreach($logs as $log)
            {
                $userString = "";
                $id = $log->forProcessID;
                $visitorString ="";
                switch($log->action)
                {
                    case"updated_user_role":
                    case"updated_work_permission_user":
                    case"updated_entry_permission_user":
                    case"deleted_user":
                        $user = User::select(DB::raw("Concat(Concat(users.forename, ' '),users.surname) AS User"))->withTrashed()->find($log->forProcessID);
                        if($user)
                        {
                            $userString = $user->User;
                        }
                        break;
                    case"create_visitor":
                    case"update_visitor":
                    case"deleted_visitor":
                        $visitor = visitor::select(DB::raw("Concat(Concat(forename, ' '),surname) AS Visitor"))->find($log->forProcessID);
                        if($visitor)
                        {
                            $visitorString = $visitor->Visitor;
                        }
                        break;
                    case"deleted__advance_registration":
                    case"deleted_advance_registration":
                        $visitIdid = advanceRegistration::select('visitId')->find($id);
                        if($visitIdid)
                        {
                            $id = $visitIdid->visitId;
                        }
                        break;
                };
                $log->action = __('history_action_log.' . $log->action, ['name' => $log->User, 'user' => $userString, 'id' => $id, 'visitor' => $visitorString]);

            }

        return $this->test(view('historyActionLog')->with('data', $logs)->with('pagitems', $items)->with('search', $request['search']));
    }

    public function visits(Request $request)
    {
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

        $requestData = [
            'startDate' => '',
            'endDate' => '',
            'activeVisits' => $request['activeVisits'],
            'visitorDetail' => $request['visitorDetail'],
            'search' => $request['search'],
        ];
        if(!empty($request['von']))
        {
            $requestData['startDate'] = date('Y-m-d', strtotime($request['von']));
        }
        else if(!$request->exists('von'))
        {
            $requestData['startDate'] = date('Y-m-d', strtotime(now() . ' -1 months'));
        }
        if (!empty($request['bis']))
        {
            $requestData['endDate'] = date('Y-m-d', strtotime($request['bis']));
        }
        $data = $this->getVisitData($request, $requestData, $items, (bool)$request['activeVisits']);

        return $this->test(view('historyVisitsLog')
                    ->with('data', $data)
                    ->with('pagitems', $items)
                    ->with('search', $request['search'])
                    ->with('requestData', $requestData));
    }

    private function getVisitData($request, $requestData, $items, $activeOnly, $csv = false)
    {
        $visitsSelect = DB::Raw
        ("
            visits.isgroup AS party,
            visits.id,
            visits.startDate,
            visits.endDate,
            visitors.company AS Company,
            Concat(Concat(visitors.forename, ' '),visitors.surname) AS Visitor,
            visitors.visitorCategory AS visitorCategory,
            visitors.visitorDetail AS visitorDetail,
            users.name AS name,
            visits.visitId,
            visits.entrypermission,
            visits.workPermission,
            advance_registrations.created_at as created_at,
            visits.updated_at,
            'visit' as itemCatagory,
            visitorallocation.allocationid
        ");

        $csvSelect = DB::Raw
        ("
            startDate,
            endDate,
            visitors.company AS Company,
            Concat(Concat(visitors.forename, ' '),visitors.surname) AS Visitor,
            visitors.visitorCategory AS visitorCategory,
            visitors.visitorDetail AS visitorDetail,
            visitorallocation.allocationid
        ");

        $advanceRegistrationSelect = DB::Raw
        ("
            party,
            advance_registrations.id,
            advance_registrations.startDate,
            advance_registrations.endDate,
            visitors.company AS Company,
            Concat(Concat(visitors.forename, ' '),visitors.surname) AS Visitor,
            visitors.visitorCategory AS visitorCategory,
            visitors.visitorDetail AS visitorDetail,
            users.name AS name,
            advance_registrations.visitId,
            advance_registrations.entrypermission,
            advance_registrations.workPermission,
            advance_registrations.created_at as created_at,
            advance_registrations.updated_at,
            'advancedRegistration' as itemCatagory,
            visitorallocation.allocationid
        ");
        $date = new DateTime("now", new DateTimeZone('Europe/Berlin'));

        if($csv)
        {
            $data = visit::join('users','visits.userId','=','users.id')
            ->join('visitorallocation','visits.visitorallocationid','=','visitorallocation.allocationid')
            ->join('visitors', 'visitorallocation.visitorid', '=', 'visitors.id')
            ->select
            (
                $csvSelect
            )
            ->where( function ($query) use ($requestData) {
                $query->where( function ($query) use ($requestData)
                {
                    if (!empty($requestData['startDate']) && !empty($requestData['endDate']))
                    {
                        $endDate = new Carbon($requestData['endDate']);
                        $startDate = new Carbon($requestData['startDate']);
                        $query->whereBetween('visits.startDate', [$startDate,$endDate])
                            ->whereBetween('visits.endDate', [$startDate,$endDate->addDay()]);
                    }
                    elseif (!empty($requestData['startDate']) && empty($requestData['endDate']))
                    {
                        $startDate = new Carbon($requestData['startDate']);
                        $query->whereDate('visits.startDate','>=', $startDate);
                    }
                    elseif (empty($requestData['startDate']) && !empty($requestData['endDate']))
                    {
                        $endDate = new Carbon($requestData['endDate']);
                        $query->whereDate('visits.endDate','<=', $endDate);
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
                                ->orWhere('visits.visitId','LIKE', '%' . substr($requestData['search'],0,strpos($requestData['search'], '-')) . '%');
                        }
                        else
                        {
                            $query->orWhere('visitors.forename','LIKE', '%' . $search . '%')
                                ->orWhere('visitors.surname','LIKE', '%' . $search . '%')
                                ->orWhere('visitors.company','LIKE', '%' . $search . '%')
                                ->orWhere('visits.visitId','LIKE', '%' . $requestData['search'] . '%');
                        }
                    }
                });
                $query->where( function ($query) use ($requestData)
                {
                    if(!empty($requestData['visitorDetail']))
                    {
                        $query->where('visitors.visitorDetail','=', $requestData['visitorDetail']);
                    }
                });
            })
            ->where('visitorallocation.leader', '=', '1')
            ->orderBy('visits.created_at', 'desc')
            ->get();
        }
        else
        {
            if($activeOnly)
            {
                $data = visit::join('users','visits.userId','=','users.id')
                ->join('visitorallocation','visits.visitorallocationid','=','visitorallocation.allocationid')
                ->join('visitors', 'visitorallocation.visitorid', '=', 'visitors.id')
                ->join('advance_registrations', 'advance_registrations.visitId', '=', 'visits.visitId')
                ->select
                (
                    $visitsSelect
                )
                ->where('visits.startDate', '<=', $date->format('Y-m-d H:i:s'))
                ->where('visits.endDate', '>=', $date->format('Y-m-d H:i:s'))
                ->where( function ($query) use ($requestData) {
                    $query->where( function ($query) use ($requestData)
                    {
                        if (!empty($requestData['startDate']) && !empty($requestData['endDate']))
                        {
                            $endDate = new Carbon($requestData['endDate']);
                            $startDate = new Carbon($requestData['startDate']);
                            $query->whereBetween('visits.startDate', [$startDate,$endDate])
                                ->whereBetween('visits.endDate', [$startDate,$endDate->addDay()]);
                        }
                        elseif (!empty($requestData['startDate']) && empty($requestData['endDate']))
                        {
                            $startDate = new Carbon($requestData['startDate']);
                            $query->whereDate('visits.startDate','>=', $startDate);
                        }
                        elseif (empty($requestData['startDate']) && !empty($requestData['endDate']))
                        {
                            $endDate = new Carbon($requestData['endDate']);
                            $query->whereDate('visits.endDate','<=', $endDate);
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
                                    ->orWhere('visits.visitId','LIKE', '%' . substr($requestData['search'],0,strpos($requestData['search'], '-')) . '%');
                            }
                            else
                            {
                                $query->orWhere('visitors.forename','LIKE', '%' . $search . '%')
                                    ->orWhere('visitors.surname','LIKE', '%' . $search . '%')
                                    ->orWhere('visitors.company','LIKE', '%' . $search . '%')
                                    ->orWhere('visits.visitId','LIKE', '%' . $requestData['search'] . '%');
                            }
                        }
                    });
                    $query->where( function ($query) use ($requestData)
                    {
                        if(!empty($requestData['visitorDetail']))
                        {
                            $query->where('visitors.visitorDetail','=', $requestData['visitorDetail']);
                        }
                    });
                })
                ->where('visitorallocation.leader', '=', '1')
                ->orderBy($request->sort ? $request->sort : 'created_at', $request->direction ? $request->direction : 'desc')
                ->paginate($items);
            }
            else
            {
                $visits = visit::join('users','visits.userId','=','users.id')
                ->join('visitorallocation','visits.visitorallocationid','=','visitorallocation.allocationid')
                ->join('visitors', 'visitorallocation.visitorid', '=', 'visitors.id')
                ->join('advance_registrations', 'advance_registrations.visitId', '=', 'visits.visitId')
                ->select
                (
                    $visitsSelect
                )
                ->where('visits.startDate', '>=', date('Y-m-d H:i:s', strtotime(now() . ' -1 year')))
                ->where( function ($query) use ($requestData) {
                    $query->where( function ($query) use ($requestData)
                    {
                        if (!empty($requestData['startDate']) && !empty($requestData['endDate']))
                        {
                            $endDate = new Carbon($requestData['endDate']);
                            $startDate = new Carbon($requestData['startDate']);
                            $query->whereBetween('visits.startDate', [$startDate,$endDate])
                                ->whereBetween('visits.endDate', [$startDate,$endDate->addDay()]);
                        }
                        elseif (!empty($requestData['startDate']) && empty($requestData['endDate']))
                        {
                            $startDate = new Carbon($requestData['startDate']);
                            $query->whereDate('visits.startDate','>=', $startDate);
                        }
                        elseif (empty($requestData['startDate']) && !empty($requestData['endDate']))
                        {
                            $endDate = new Carbon($requestData['endDate']);
                            $query->whereDate('visits.endDate','<=', $endDate);
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
                                    ->orWhere('visits.visitId','LIKE', '%' . substr($requestData['search'],0,strpos($requestData['search'], '-')) . '%');
                            }
                            else
                            {
                                $query->orWhere('visitors.forename','LIKE', '%' . $search . '%')
                                    ->orWhere('visitors.surname','LIKE', '%' . $search . '%')
                                    ->orWhere('visitors.company','LIKE', '%' . $search . '%')
                                    ->orWhere('visits.visitId','LIKE', '%' . $requestData['search'] . '%');
                            }
                        }
                    });
                    $query->where( function ($query) use ($requestData)
                    {
                        if(!empty($requestData['visitorDetail']))
                        {
                            $query->where('visitors.visitorDetail','=', $requestData['visitorDetail']);
                        }
                    });
                })
                ->where('visitorallocation.leader', '=', '1');

                $data = advanceRegistration::join('users','advance_registrations.userId','=','users.id')
                ->join('visitorallocation','advance_registrations.allocationid','=','visitorallocation.allocationid')
                ->join('visitors', 'visitorallocation.visitorid', '=', 'visitors.id')
                ->leftJoin('visits', 'advance_registrations.visitId', '=', 'visits.visitId')
                ->select
                (
                    $advanceRegistrationSelect
                )
                ->whereNull("visits.id")
                ->where( function ($query) use ($request){
                    if($request['myAdvanceRegistration'])
                    {
                        $query->where('userId','=', Auth::user()->id);
                    }
                })
                ->where('visitorallocation.leader', '=', '1')
                ->where('advance_registrations.startDate', '>=', date('Y-m-d H:i:s', strtotime(now() . ' -1 year')))
                ->where( function ($query) use ($requestData)
                {
                    $query->where( function ($query) use ($requestData)
                    {
                        if (!empty($requestData['startDate']) && !empty($requestData['endDate']))
                        {
                            $endDate = new Carbon($requestData['endDate']);
                            $startDate = new Carbon($requestData['startDate']);
                            $query->whereBetween('advance_registrations.startDate', [$startDate,$endDate])
                                ->whereBetween('advance_registrations.endDate', [$startDate,$endDate->addDay()]);
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
                    $query->where( function ($query) use ($requestData)
                    {
                        if(!empty($requestData['visitorDetail']))
                        {
                            $query->where('visitors.visitorDetail','=', $requestData['visitorDetail']);
                        }
                    });

                })
                ->union($visits)
                ->orderBy($request->sort ? $request->sort : 'created_at', $request->direction ? $request->direction : 'desc')
                ->paginate($items);
            }
        }

        foreach ($data as $dat)
        {
            $dat->updated_at = Carbon::createFromFormat('Y-m-d H:i:s', $dat->updated_at, 'UTC')->setTimezone("Europe/Berlin");
            $dat->created_at = Carbon::createFromFormat('Y-m-d H:i:s', $dat->created_at, 'UTC')->setTimezone("Europe/Berlin");
        }
        foreach($data as $item)
        {
            if($item->itemCatagory == "visit" || $csv)
            {
                $allocation = DB::table('visitorallocation')->select( DB::raw('Count(id) as amount'))->where("allocationid", $item->allocationid)->first();
                $item->presenceTime = $this->getTimes($item->startDate ,$item->endDate, $allocation->amount);
            }
        }

        return $data;
    }

    private function getTimes($startDate, $endDate, $amount)
    {
        $startDate = new DateTime($startDate);
        $endDate =  new DateTime($endDate);
        if($startDate->format('Y-m-d') == $endDate->format('Y-m-d'))
        {
            $diff = $startDate->diff($endDate);
            return $diff->h * $amount . ":" . $diff->i * $amount . ":" . $diff->s * $amount;
        }
        else
        {
            $diff = $startDate->diff($endDate);
            $intervalInSeconds = (new DateTime())->setTimeStamp(0)->add($diff)->getTimeStamp();
            $intervalInDays = $intervalInSeconds/86400;
            return (ceil($intervalInDays)) * 8 * $amount . ":00:00";
        }
    }

    public function ExportCONTimes(Request $request)
    {
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

        $requestData = [
            'startDate' => '',
            'endDate' => '',
            'activeVisits' => $request['activeVisits'],
            'visitorDetail' => $request['visitorDetail'],
            'search' => $request['search'],
        ];
        if(!empty($request['von']))
        {
            $requestData['startDate'] = date('Y-m-d', strtotime($request['von']));
        }
        if (!empty($request['bis']))
        {
            $requestData['endDate'] = date('Y-m-d', strtotime($request['bis']));
        }

        $data = $this->getVisitData($request, $requestData, $items, $request['activeVisits'] == "false" ? false : true, true);

        $name = 'exports/' . Auth::user()->name . "-" . now(new DateTimeZone('Europe/Berlin'))->format('Y-m-d H-i') . '.csv';
        $handle = fopen($name, 'w');
        foreach ($data as $row)
        {
            $array = $row->toArray();
            unset($array['allocationid']);
            fputcsv($handle, $array, ';');
        }
        fclose($handle);
        return asset($name);
    }

}
