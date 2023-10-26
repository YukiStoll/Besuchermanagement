<?php

namespace App\Http\Controllers;

use App\advanceRegistration;
use App\history_action_log;
use App\holidays;
use App\visit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProfileController extends UNOController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $holiday = holidays::select('from', 'to')->where("userID", "=", Auth::user()->id)->first();
        $from = "";
        $to = "";
        if($holiday)
        {
            $from = $holiday->from;
            $to = $holiday->to;
        }
        return view('profile')->with([
            "forename" => Auth::user()->forename,
            "surname" => Auth::user()->surname,
            "email" => Auth::user()->email,
            "department" => Auth::user()->department,
            "telephone_number" => Auth::user()->telephone_number,
            "mobile_number" => Auth::user()->mobile_number,
            "holiday_from" => $from,
            "holiday_to" => $to,
             ]);
    }

    public function uno()
    {
        $holiday = holidays::select('from', 'to')->where("userID", "=", Auth::user()->id)->first();
        $from = "";
        $to = "";
        if($holiday)
        {
            $from = $holiday->from;
            $to = $holiday->to;
        }
        return view('profile')->with([
            "forename" => Auth::user()->forename,
            "surname" => Auth::user()->surname,
            "email" => Auth::user()->email,
            "department" => Auth::user()->department,
            "telephone_number" => Auth::user()->telephone_number,
            "mobile_number" => Auth::user()->mobile_number,
            "holiday_from" => $from,
            "holiday_to" => $to,
            "edit" => true,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            /*'forename' => 'required',
            'surname' => 'required',
            'email' => 'required|email',
            'department' => 'required',
            'telephone_number' => 'required',*/
            'mobile_number' => 'required',
        ]);
        if(!empty($request->holidayFrom))
        {
            $request->validate([
                'holidayFrom' => 'required|date',
                'holidayTo' => 'required|date|after_or_equal:holidayFrom',
            ]);
            holidays::updateOrCreate(["userID" => $request->id], ["from" => $request->holidayFrom, "to" => $request->holidayTo]);
            history_action_log::insert(["userID" => $request->id, "action" => "set_holiday"]);
        }
        else
        {
            $holidayDelete = holidays::where('userID', $request->id)->first();
            if($holidayDelete)
            {
                $holidayDelete->delete();
            }
        }
        $user = DB::table('users')
            ->where('id',  $request->id)
            ->update(
                [
                /*'forename' => $request->input('forename'),
                'surname' => $request->input('surname'),
                'email' => $request->input('email'),
                'department' => $request->input('department'),
                'telephone_number' => $request->input('telephone_number'),*/
                'mobile_number' => $request->input('mobile_number'),
                ]);
        $holiday = holidays::select('from', 'to')->where("userID", "=", Auth::user()->id)->first();
        $from = "";
        $to = "";
        if($holiday)
        {
            $from = $holiday->from;
            $to = $holiday->to;
        }
        history_action_log::insert(["userID" => $request->id, "action" => "saved_profile"]);
        if($user)
        {
            return view('profile')->with([
                "forename" => $request->input('forename'),
                "surname" => $request->input('surname'),
                "email" => $request->input('email'),
                "department" => $request->input('department'),
                "telephone_number" => $request->input('telephone_number'),
                "mobile_number" => $request->input('mobile_number'),
                "holiday_from" => $from,
                "holiday_to" => $to,
                "success" => 'true',
            ]);
        }
        else
        {
            return view('profile')->with([
                "forename" => $request->input('forename'),
                "surname" => $request->input('surname'),
                "email" => $request->input('email'),
                "department" => $request->input('department'),
                "telephone_number" => $request->input('telephone_number'),
                "mobile_number" => $request->input('mobile_number'),
                "holiday_from" => $from,
                "holiday_to" => $to,
                "success" => 'false',
            ]);
        }
    }

    public function storeuno(Request $request)
    {
        $request->validate([
            'forename' => 'required',
            'surname' => 'required',
            'email' => 'required|email',
            'department' => 'required',
            'telephone_number' => 'required',
        ]);
        if(!empty($request->holidayFrom))
        {
            $request->validate([
                'holidayFrom' => 'required|date',
                'holidayTo' => 'required|date|after_or_equal:holidayFrom',
            ]);
            holidays::updateOrCreate(["userID" => $request->id], ["from" => $request->holidayFrom, "to" => $request->holidayTo]);
            history_action_log::insert(["userID" => $request->id, "action" => "set_holiday"]);
        }
        else
        {
            $holidayDelete = holidays::where('userID', $request->id)->first();
            if($holidayDelete)
            {
                $holidayDelete->delete();
            }
        }
        $user = DB::table('users')
            ->where('id', $request->id)
            ->update(
                [
                    'forename' => $request->input('forename'),
                    'surname' => $request->input('surname'),
                    'email' => $request->input('email'),
                    'department' => $request->input('department'),
                    'telephone_number' => $request->input('telephone_number'),
                    'mobile_number' => $request->input('mobile_number'),
                ]);


        $holiday = holidays::select('from', 'to')->where("userID", "=", Auth::user()->id)->first();
        $from = "";
        $to = "";
        if($holiday)
        {
            $from = $holiday->from;
            $to = $holiday->to;
        }
        history_action_log::insert(["userID" => $request->id, "action" => "saved_profile"]);
        if($user)
        {
            return view('profile')->with([
                "forename" => $request->input('forename'),
                "surname" => $request->input('surname'),
                "email" => $request->input('email'),
                "department" => $request->input('department'),
                "telephone_number" => $request->input('telephone_number'),
                "mobile_number" => $request->input('mobile_number'),
                "holiday_from" => $from,
                "holiday_to" => $to,
                "success" => 'true',
            ]);
        }
        else
        {
            return view('profile')->with([
                "forename" => $request->input('forename'),
                "surname" => $request->input('surname'),
                "email" => $request->input('email'),
                "department" => $request->input('department'),
                "telephone_number" => $request->input('telephone_number'),
                "mobile_number" => $request->input('mobile_number'),
                "holiday_from" => $from,
                "holiday_to" => $to,
                "success" => 'false',
            ]);
        }
    }

    public function hasVisits($id, Request $request)
    {
        $allocations = DB::table('userallocation')->select('allocationID')->where('userID', '=', $id)->pluck('allocationID');
        if($allocations)
        {
            $responseArray = [
                'success' => true
            ];

            $visits = visit::select('visitId', 'startDate', 'endDate', 'visitorallocationid')
            ->whereIn('visitorallocationid', $allocations)
            ->where('deleted_at', '=', null)
            ->where(function ($query) use ($request)
            {
                $query->where(function ($lower_query1) use ($request)
                {
                    $lower_query1->where('startDate', '<=', $request->startDate)
                                ->where('endDate', '>=', $request->startDate);
                })
                ->orWhere(function ($lower_query2) use ($request)
                {
                    $lower_query2->where('startDate', '<=', $request->endDate)
                                ->where('endDate', '>=', $request->endDate);
                })
                ->orWhere(function ($lower_query3) use ($request)
                {
                    $lower_query3->where('startDate', '>=', $request->startDate)
                                ->where('endDate', '<=', $request->endDate);
                });
            })->get();
            if(!$visits->isEmpty())
            {
                $responseArray['visits'] = $visits;
            }
            else
            {
                $responseArray['visits'] = 'no_visits';
            }

            $advanceregistrations = advanceRegistration::select('visitId', 'startDate', 'endDate')
            ->whereIn('allocationid', $allocations)
            ->where('deleted_at', '=' ,null)
            ->where(function ($query) use ($request)
            {
                $query->where(function ($lower_query1) use ($request)
                {
                    $lower_query1->where('startDate', '<=', $request->startDate)
                                ->where('endDate', '>=', $request->startDate);
                })
                ->orWhere(function ($lower_query2) use ($request)
                {
                    $lower_query2->where('startDate', '<=', $request->endDate)
                                ->where('endDate', '>=', $request->endDate);
                })
                ->orWhere(function ($lower_query3) use ($request)
                {
                    $lower_query3->where('startDate', '>=', $request->startDate)
                                ->where('endDate', '<=', $request->endDate);
                });
            })->get();
            if(!$advanceregistrations->isEmpty())
            {
                $responseArray['advanceregistrations'] = $advanceregistrations;
            }
            else
            {
                $responseArray['advanceregistrations'] = 'no_advanceregistrations';
            }
            return response()->json($responseArray);
        }
        else
        {
            return response()->json([
                'message' => 'no_allocations',
                'success' => false
            ]);
        }
    }

}
