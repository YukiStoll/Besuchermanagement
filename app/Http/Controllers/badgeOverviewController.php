<?php

namespace App\Http\Controllers;

use App\mawa_persons;
use App\visitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class badgeOverviewController extends UNOController
{
    public function index(Request $request)
    {
        /*
        area_permission_status_allocation
        ->join('contacts', function ($join) {
            $join->on('users.id', '=', 'contacts.user_id')->orOn(...);
        })
        */
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
        $search = $request['search'];
        $persons = mawa_persons::select("firstName", "lastName", "type", "validFrom", "validTo", "cardID", "doors", DB::raw("'false' as createdThroughSystem"), DB::raw("'0' as visitID"))
        ->where(function ($query) use ($search)
        {
            $query->where('firstName','LIKE', '%' . $search . '%')
            ->orWhere('lastName','LIKE', '%' . $search . '%')
            ->orWhere('cardID','LIKE', '%' . $search . '%');
        });

        $visitors = visitor::select("visitors.forename as firstName", "visitors.surname as lastName", "visitors.visitorCategory as type", "visits.startDate as validFrom", "visits.endDate as validTo", "visitorallocation.cardId as cardID", DB::raw("'test' as doors"), DB::raw("'true' as createdThroughSystem"), 'visits.id as visitID')
        ->join('visitorallocation', 'visitorallocation.visitorid', 'visitors.id')
        ->join('visits', 'visits.visitorallocationid', 'visitorallocation.allocationid')
        ->whereNotNull("visitorallocation.cardId")
        ->where(function ($query) use ($search)
        {
            $query->where('visitors.forename','LIKE', '%' . $search . '%')
            ->orWhere('visitors.surname','LIKE', '%' . $search . '%')
            ->orWhere('visitorallocation.cardId','LIKE', '%' . $search . '%');
        })
        ->union($persons)
        ->sortable('validFrom')
        ->paginate($items);

        return $this->test(view('badgeOverview')->with("data", $visitors)->with('pagitems', $items)->with('search', $request['search']));
    }
}
