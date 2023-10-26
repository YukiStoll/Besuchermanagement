<?php

namespace App\Http\Controllers;

use App\area_permission_allocation;
use App\areaPermission;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class areaPermissionController extends UNOController
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
        $areapermissions = areaPermission::select("*")
        ->sortable(['name' => 'asc'])
        ->paginate($items);
        return $this->test(View('areaPermission'))->with("areapermissions", $areapermissions)->with('pagitems', $items);
    }

    public function edit($id, $success = "")
    {
        $areapermissions = areaPermission::find($id);
        $users = DB::table("area_permission_allocation")
        ->join("users", "users.id", "=", "area_permission_allocation.userID")
        ->select("area_permission_allocation.id", "area_permission_allocation.areapermissionID", "area_permission_allocation.userID", "users.forename", "users.surname", "area_permission_allocation.position")
        ->where("areapermissionID", "=", $id)
        ->orderBy("area_permission_allocation.position")
        ->get();
        return $this->test(View('areaPermissionEdit'))->with("areapermissions", $areapermissions)->with("users", $users)->with("success", $success);
    }

    public function new()
    {
        $areapermissions = areaPermission::create();
        return $this->test(View('areaPermissionNew'))->with("areapermissions", $areapermissions);
    }

    public function save(Request $request)
    {

    }

    public function update($id, Request $request)
    {
        $request->validate([
            'name' => 'required',
            'mawaID' => 'required',
        ]);
        $areaPermission = areaPermission::find($id);
        $areaPermission->name = $request['name'];
        $areaPermission->mawaID = $request['mawaID'];
        $areaPermission->save();
        return $this->edit($id, "savedSuccess");
    }

    public function removeAreaPermission($id)
    {
        area_permission_allocation::where('areapermissionID', $id)->delete();
        $deleted2 = areaPermission::destroy($id);
        if($deleted2)
        {
            return response()->json("success");
        }
        return response()->json("failed");
    }

    public function addNewUser(Request $request)
    {
        $newareapermission = area_permission_allocation::create([
            'areapermissionID' => $request['areapermissionID'],
            'userID' => $request['userID'],
            'position' => $request['position'],
        ]);

        return response()->json($newareapermission);
    }

    public function removeUser($id)
    {
        $areaPermission = area_permission_allocation::find($id);
        $areapermissionID = $areaPermission->areapermissionID;
        $userID = $areaPermission->userID;
        $position = $areaPermission->position;
        $positonAreaPermissionAllocations = area_permission_allocation::where("areapermissionID", $areapermissionID)->max("position");
        for($i = 1; $i <= $positonAreaPermissionAllocations - $position; $i++)
        {
            $change = area_permission_allocation::select("id", "position")->where("areapermissionID", $areapermissionID)->where("position", $position + $i)->first();
            $change->position = $change->position - 1;
            $change->save();
        }
        area_permission_allocation::destroy($id);
        

        return response()->json("success");
    }

    public function mawaChangeUserPosition($id, Request $request)
    {
        $permissionAllocation = area_permission_allocation::find($id);
        $permissionAllocation->position = $request['position'];
        $permissionAllocation->save();

        return response()->json("success");
    }
}
