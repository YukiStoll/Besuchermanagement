<?php

namespace App\Http\Controllers;

use App\history_action_log;
use App\visitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VisitorAPIController extends Controller
{

    public function __construct()
    {
        //$this->middleware('auth');
    }

    public function index()
    {
        $visitor = visitor::all();

        return response()->json($visitor);
    }

    public function create(Request $request)
    {

    }

    public function store(Request $request)
    {
        $now = date('d.m.Y');
        $request->validate([
            'forename' => 'required',
            'surname' => 'required',
            'salutation' => 'required',
            'email' => 'required|email',
            'language' => 'required',
            'visitorCategory' => 'required',
            'visitorDetail' => 'required',
            'company' => 'required',
            'companyStreet' => 'required',
            'companyCountry' => 'required',
            'companyZipCode' => 'required',
            'companyCity' => 'required',
            'creator' => 'required',
            'landlineNumber' => 'required',
        ]);
        $visitorWithSameName = DB::table('visitors')
                                    ->select('id')
                                    ->where('forename','=', $request['forename'])
                                    ->where('surname','=', $request['surname'])
                                    ->where('email','=', $request['email'])
                                    ->whereNull('deleted_at')
                                    ->first();
        if(!empty($visitorWithSameName))
        {
            $request->validate([
                'stillCreate' => 'required',
            ]);
        }

        $visitor = visitor::create($request->all());

        history_action_log::insert(["userID" => Auth::user()->id, "action" => "create_visitor", "forProcessID" => $visitor->id]);
        if ($visitor)
        {
            if(request()->wantsJson())
            {
                return response()->json($visitor);
            }
            return redirect()->back()->with('success', true);
        }
        else
        {
            if(request()->wantsJson())
            {
                return response()->json("failed");
            }
            return redirect()->back()->with('success', false);
        }

    }

    public function search(Request $request)
    {
        $query_request = $request->get('query');
        $visitor = visitor::select(DB::raw("CONCAT(`forename`, ' ', `surname`, ' ', `company`) as term"),'surname', 'forename', 'company', 'id')
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
                        ["company", "Like", "%{$query_requests[0]}%"],
                    ])->orWhere([
                        ["company", "Like", "%{$query_requests[1]}%"],
                        ["forename", "Like", "%{$query_requests[0]}%"],
                    ])->orWhere([
                        ["surname", "Like", "%{$query_requests[1]}%"],
                        ["forename", "Like", "%{$query_requests[0]}%"],
                    ])->orWhere([
                        ["surname", "Like", "%{$query_requests[0]}%"],
                        ["company", "Like", "%{$query_requests[1]}%"],
                    ])->orWhere([
                        ["company", "Like", "%{$query_requests[0]}%"],
                        ["forename", "Like", "%{$query_requests[1]}%"],
                    ]);
                }
                else if(count($query_requests) == 3)
                {
                    $query->where([
                        ["surname", "Like", "%{$query_requests[0]}%"],
                        ["forename", "Like", "%{$query_requests[1]}%"],
                        ["company", "Like", "%{$query_requests[2]}%"],
                    ])->orwhere([
                        ["surname", "Like", "%{$query_requests[1]}%"],
                        ["forename", "Like", "%{$query_requests[0]}%"],
                        ["company", "Like", "%{$query_requests[2]}%"],
                    ])->orwhere([
                        ["surname", "Like", "%{$query_requests[1]}%"],
                        ["forename", "Like", "%{$query_requests[2]}%"],
                        ["company", "Like", "%{$query_requests[0]}%"],
                    ])->orwhere([
                        ["surname", "Like", "%{$query_requests[0]}%"],
                        ["forename", "Like", "%{$query_requests[2]}%"],
                        ["company", "Like", "%{$query_requests[1]}%"],
                    ])->orwhere([
                        ["surname", "Like", "%{$query_requests[2]}%"],
                        ["forename", "Like", "%{$query_requests[0]}%"],
                        ["company", "Like", "%{$query_requests[1]}%"],
                    ])->orwhere([
                        ["surname", "Like", "%{$query_requests[2]}%"],
                        ["forename", "Like", "%{$query_requests[1]}%"],
                        ["company", "Like", "%{$query_requests[0]}%"],
                    ]);
                }
                else
                {
                    $query->whereRaw("MATCH (surname) AGAINST ('%{$query_request}%')")
                    ->orwhereRaw("MATCH (forename) AGAINST ('%{$query_request}%')")
                    ->orwhereRaw("MATCH (company) AGAINST ('%{$query_request}%')")
                    ->orwhere('surname', 'like', "%{$query_request}%")
                    ->orwhere('forename', 'like', "%{$query_request}%")
                    ->orwhere('company', 'like', "%{$query_request}%");
                }

            })
            ->whereNull('deleted_at')
            ->whereNull('deleted_from_id')
            ->limit(100)
            ->orderBy('surname', 'asc')
            ->get();

        return response()->json($visitor);
    }

    public function showGroup(Request $request)
    {
        $visitorGroupIDs = DB::table('visitorallocation')->select('visitorid')->where('allocationid', '=', $request['ID'])->get();

        foreach($visitorGroupIDs as $visitorID)
        {
            $visitors[] = visitor::all()->find($visitorID->visitorid);
        }

        if(!empty($visitors))
        {
            return response()->json($visitors);
        }
        else
        {
            return response()->json($request['ID']);
        }
    }

    public function show($id)
    {
        if($visitor = visitor::all()->find($id))
        {
            return response()->json($visitor);
        }
        else
        {
            return response()->json($id);
        }
    }

    public function edit(visitor $visitor)
    {
    }

    public function update(Request $request, $id)
    {
        $now = date('d.m.Y');
        $request->validate([
            'forename' => 'required',
            'surname' => 'required',
            'salutation' => 'required',
            'title' => 'nullable',
            'email' => 'required|email',
            'language' => 'required',
            'visitorCategory' => 'required',
            'visitorDetail' => 'required',
            'company' => 'required',
            'companyStreet' => 'required',
            'companyCountry' => 'required',
            'companyZipCode' => 'required',
            'companyCity' => 'required',
            'landlineNumber' => 'required',
        ]);

        if($visitor = visitor::all()->find($id))
        {
            $visitor->update($request->all());
            history_action_log::insert(["userID" => Auth::user()->id, "action" => "update_visitor", "forProcessID" => $id]);
            return response()->json($visitor);
        }
        else
        {
            return response()->json($id);
        }



    }

    public function destroy($id, Request $request)
    {
        $request->validate([
            'deleted_at' => 'required',
            'deleted_from_id' => 'required',
        ]);

        if($visitor = visitor::all()->find($id))
        {
            $visitor->update($request->all());
            history_action_log::insert(["userID" => Auth::user()->id, "action" => "deleted_visitor", "forProcessID" => $id]);
            return response()->json($visitor);
        }
        else
        {
            return response()->json($id);
        }
    }
}
