<?php

namespace App\Http\Controllers;

use App\history_action_log;
use App\visitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class myVisitorsController extends UNOController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function search(Request $request)
    {
        return $this->getVisitors($request, 'myVisitors');
    }
    public function admin(Request $request)
    {
        return $this->getVisitors($request, 'visitorAdminTable');
    }
    public function deleteCompleteVisitor(Request $request)
    {
        $visitor = DB::table('visitors')
            ->where('id', '=', $request['id'])
            ->delete();

        history_action_log::insert(["userID" => Auth::user()->id, "action" => "deleted_visitor", "forProcessID" => $request['id']]);
        if(!$visitor)
        {
            return 'Visitor not found';
        }
        $allocationIds = DB::table('visitorallocation')
            ->select("allocationid")
            ->where('visitorid','=', $request['id'])
            ->get();
        foreach($allocationIds as $id)
        {
            $advanceRegistration = DB::table('advance_registrations')
                ->where('allocationid','=', $id->allocationid)
                ->delete();
            $visit = DB::table('visits')
                ->where('visitorallocationid','=', $id->allocationid)
                ->delete();
            $allocation = DB::table('visitorallocation')
                ->where('allocationid','=', $id->allocationid)
                ->delete();
            if(!$advanceRegistration || !$visit || !$allocation)
            {
                return 'Something went wrong!';
            }
        }
        return response()->json("Successfully deleted");
    }

    public function getVisitors($request, $view)
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
        $visitors = visitor::select(DB::RAW("id, forename, surname, visitorCategory, visitorDetail, company, email, landlineNumber"))
            ->whereNull("deleted_at")
            ->where( function ($query) use ($request)
            {
                $searchArray = explode(" ", $request['search']);
                foreach ($searchArray as $search)
                {
                    $query->orWhere('forename','LIKE','%' . $search . '%')
                        ->orWhere('surname','LIKE','%' . $search . '%')
                        ->orWhere('visitorCategory','LIKE','%' . $search . '%')
                        ->orWhere('company','LIKE','%' . $search . '%');
                }
            })
            ->where( function ($query) use ($request){
                if($request['myVisitor'])
                {
                    $query->where('creator','=', Auth::user()->id);
                }
            })
            ->sortable(['created_at' => 'desc'])
            ->paginate($items);
            Log::debug($visitors);
        return $this->test(view($view)->with('data', $visitors)->with('pagitems', $items)->with('search', $request['search'])->with("myVisitors", $request['myVisitor']));
    }
    public function safetyInstructionQuestions($id)
    {
        $visitor = $this->getSafetyQuestions($id);
        return $this->test(view('failedSafetyQuestionsReport')->with('questionsSafetyInstructions', $visitor["questionsSafetyInstructions"])->with('id', $id)->with('visitor', $visitor["visitor"]));
    }
    public function printTestResults($id, $short)
    {
        $visitor = $this->getSafetyQuestions($id);
        return $this->test(view('prints.testResult')->with('visitor', $visitor["visitor"])->with('short', $short)->with('questionsSafetyInstructions', $visitor["questionsSafetyInstructions"]));
    }
    public function getSafetyQuestions($id)
    {
        $visitorID = substr($id, 0, strpos($id, '-'));
        $tableID = substr($id, strpos($id, '-') + 1);
        if($tableID == 1)
        {
            $visitor = visitor::select("questionsSafetyInstructions", "forename", "surname", "company")
                ->where("id", "=", $visitorID)
                ->first();
        }
        else
        {
            $visitor = DB::table('group_visitors')
                ->select("questionsSafetyInstructions", "forename", "surname")
                ->where("id", "=", $visitorID)
                ->first();
        }
        $questionsSafetyInstructions = json_decode($visitor->questionsSafetyInstructions, true);
        $visitor->questionsSafetyInstructions = "";
        $visitor = json_decode(json_encode($visitor), true);
        return [
                    'questionsSafetyInstructions' => $questionsSafetyInstructions,
                    'visitor' => $visitor
                ];
    }
}
