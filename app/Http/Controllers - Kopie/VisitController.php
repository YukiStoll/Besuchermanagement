<?php

namespace App\Http\Controllers;

use App\visit;
use App\visitor;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class VisitController extends UNOController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public  function search(Request $request)
    {
        if(Auth::user()->role != "Gatekeeper" && Auth::user()->role != "Admin" && Auth::user()->role != "Super Admin")
        {
            abort(403, 'Unauthorized action.');
            return redirect()->home();
        }
        $requestData = [
            'startDate' => '',
            'endDate' => '',
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
        $visitorAllocationIds = DB::table('visitorallocation')
            ->select("allocationid")
            ->where("cardId","=", $requestData['search'])
            ->get();
            Log::debug($visitorAllocationIds);
            Log::debug($requestData['search']);
        $cardsExist = false;
        $allocationIds = null;
        foreach ($visitorAllocationIds as $visitorAllocationId)
        {
            if(!empty($visitorAllocationId) && !empty($requestData['search']))
            {
                $cardsExist = true;
                $allocationIds[] = $visitorAllocationId->allocationid;
            }
        }

            Log::debug($cardsExist);
        if($cardsExist)
        {
            $data = visit::join('users','visits.userId','=','users.id')
                ->join('visitorallocation','visits.visitorallocationid','=','visitorallocation.allocationid')
                ->join('visitors', 'visitorallocation.visitorid', '=', 'visitors.id')
                ->select
                (
                    DB::Raw
                    ("
                    visits.id, visits.isgroup, startDate, endDate,
                    Concat(Concat(visitors.forename, ' '),visitors.surname) AS Visitor,
                    visitors.company AS Company, visitors.visitorCategory AS visitorCategory, entrypermission, workPermission,
                    users.name AS name, visitId, visitors.safetyInstruction, visitors.questionsSafetyInstructions, visitorallocation.allocationid
                    ")
                )
                ->whereNull('visits.deleted_at')
                ->whereIn("visitorallocationid", $allocationIds)
                ->where('visitorallocation.leader', '=', '1')
                ->sortable(['startDate' => 'desc'])
                ->paginate($items);
        }
        else
        {
            $data = visit::join('users','visits.userId','=','users.id')
                ->join('visitorallocation','visits.visitorallocationid','=','visitorallocation.allocationid')
                ->join('visitors', 'visitorallocation.visitorid', '=', 'visitors.id')
                ->select
                (
                    DB::Raw
                    ("
                    visits.id, visits.isgroup, startDate, endDate,
                    Concat(Concat(visitors.forename, ' '),visitors.surname) AS Visitor,
                    visitors.company AS Company, visitors.visitorCategory AS visitorCategory, entrypermission, workPermission,
                    users.name AS name, visitId, visitors.safetyInstruction, visitors.questionsSafetyInstructions, visitorallocation.allocationid
                    ")
                )
                ->whereNull('visits.deleted_at')
                ->where( function ($query) use ($requestData) {
                    $query->where( function ($query) use ($requestData)
                    {
                        if (!empty($requestData['startDate']) && !empty($requestData['endDate']))
                        {
                            $endDate = new Carbon($requestData['endDate']);
                            $query->whereBetween('visits.startDate', [$requestData['startDate'],$endDate])
                                ->whereBetween('visits.endDate', [$requestData['startDate'],$endDate->addDay()]);
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
                })
                ->where('visitorallocation.leader', '=', '1')
                ->sortable(['startDate' => 'desc'])
                ->paginate($items);
        }
        foreach ($data as $key => $item)
        {
            $allocation = DB::table('visitorallocation')
                ->select("visitorid")
                ->where("allocationid","=",$item->allocationid)
                ->get();
            $safetyTest = 0;
            foreach ($allocation as $visitorID)
            {
                $visitor = visitor::find($visitorID->visitorid);
                if($visitor['safetyInstruction'] == "" && $visitor['questionsSafetyInstructions'] != "")
                {
                    $safetyTest = 2;
                }
                elseif($visitor['safetyInstruction'] == "" && $visitor['questionsSafetyInstructions'] == "" && $safetyTest != 2)
                {
                    $safetyTest = 1;
                }
            }
            $data[$key]->safetyTest = $safetyTest;
        }

        return $this->test(view('visits')
            ->with('data', $data)
            ->with('pagitems', $items)
            ->with('requestData', $requestData));
    }
    public function makeSpontaneousVisit(Request $request)
    {
        $request->validate([
            'company' => 'required',
            'vehicleRegistrationNumber' => 'required',
            'orderNumber' => 'nullable',
            'cargo' => 'required',
            'reasonForVisit' => 'required',
            'carrier' => 'required',
        ]);
        $visitor = DB::table('visitors')
            ->insertGetId([
                'creator' => Auth::id(),
                'visitorCategory' => 'Lieferant',
                'company' => $request['company'],
            ]);
        if($visitor)
        {
            $allocationid = DB::table('visitorallocation')
            ->select("allocationid")->max('allocationid');
            $allocationid = $allocationid + 1;

            $allocation = DB::table('visitorallocation')
                ->insert([
                    'allocationid' => $allocationid,
                    'visitorid' => $visitor,
                    'leader' => '1',
                    'visiting' => '0',
                    'canteen' => '0',
                ]);
            if($allocation)
            {
                do
                {
                    $visitId = random_int(10000000,99999999);
                    $visitidexists = DB::table('advance_registrations')->where('visitId','=', $visitId)->first();
                } while(!empty($visitidexists));
                $visit = DB::table('visits')
                    ->insert([
                        'startDate' => date('Y-m-d H:I:s'),
                        'endDate' => date('Y-m-d H:I:s'),
                        'vehicleRegistrationNumber' => $request['vehicleRegistrationNumber'],
                        'orderNumber' => $request['orderNumber'],
                        'cargo' => $request['cargo'],
                        'carrier' => $request['carrier'],
                        'reasonForVisit' => $request['reasonForVisit'],
                        'visitorallocationid' => $allocationid,
                        'userId' => Auth::id(),
                        'visitId' => $visitId,
                        'isgroup' => '0',
                    ]);
                if($visit)
                {
                    return back()->with("success","true");
                }
                else
                {
                    return back()->with("success","false");
                }
            }
            else
            {
                return back()->with("success","false");
            }
        }
        else
        {
            return back()->with("success","false");
        }
    }
}
