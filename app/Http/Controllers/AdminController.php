<?php

namespace App\Http\Controllers;

use App\admin_setting;
use App\history_action_log;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class AdminController extends UNOController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $adminSettings = admin_setting::where("setting_type", null)->get();
        $workPermissions = admin_setting::where("setting_type", "workPermission")->get();
        return $this->test(view('adminSettings')->with(["admin_setting" => $adminSettings, "workPermissions" => $workPermissions]));
    }

    public function update(Request $request)
    {
        if(!empty($request->newWorkPermissionName))
        {
            $this->validate($request, ['newWorkPermissionFile' => 'required']);
        }

        if($request->hasFile("newWorkPermissionFile"))
        {
            $this->validate($request, ['newWorkPermissionName' => 'required']);
        }

        foreach($request->file() as $fileName => $fileInfos)
        {
            Log::debug(str_replace("workPermission_", "", $fileName));
            if(str_contains($fileName, "workPermission"))
            {
                $workPermissions[] =
                    [
                        "file" => $fileInfos,
                        "name" => str_replace("workPermission_", "", $fileName),
                    ];
            }
            else if($fileName != "newWorkPermissionFile")
            {
                $documents[] =
                    [
                        "file" => $fileInfos,
                        "name" => $fileName,
                    ];
            }
        }
        $hasWorkpermissin = false;
        if(!empty($workPermissions))
        {
            foreach ($workPermissions as $workPermission)
            {
                if($workPermission['file'] != null && $workPermission['file']->isValid() && $workPermission['file']->getClientMimeType() == 'application/pdf')
                {
                    $hasWorkpermissin = true;
                    $names[] = $workPermission['name'];
                    $workPermission['file']->move("workPermissionDocuments/documents/", $workPermission['name'] . ".pdf");
                }
            }
        }
        if($hasWorkpermissin)
        {
            history_action_log::insert(["userID" => Auth::user()->id, "action" => "updated_work_permission_documents"]);
        }

        if(!empty($documents))
        {
            foreach ($documents as $document)
            {
                if($document['file'] != null && $document['file']->isValid() && $document['file']->getClientMimeType() == 'application/pdf')
                {
                    $names[] = $document['name'];
                    $document['file']->move("documents/", $document['name'] . ".pdf");
                }
            }
        }


        foreach ($request->input() as $key => $input)
        {
            if($key != "_token" && $key != "newWorkPermissionName")
            {
                $admin_settings = admin_setting::all()->where("setting_key", "=", $key)->first();
                $request['setting_value'] = $input;
                if($admin_settings)
                {
                    $admin_settings->update($request->all());
                }
                else
                {
                    return response()->json("Failed", 400);
                }
            }
        }
        history_action_log::insert(["userID" => Auth::user()->id, "action" => "saved_admin_settings"]);
        return redirect()->route('admin.settings');


    }

    public function showUsers(Request $request)
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

        $userRole = Auth::user()->role;

        $users = User::select(DB::RAW("id, forename, surname, role, email, canIssueWorkPermit, canIssueEntryPermit"))
            ->whereNull("deleted_at")
            ->where(function ($query) use ($request)
            {
                $searchArray = explode(" ", $request['search']);
                foreach ($searchArray as $search)
                {
                    $query->orWhere('forename','LIKE','%' . $search . '%')
                        ->orWhere('surname','LIKE','%' . $search . '%')
                        ->orWhere('role','LIKE','%' . $search . '%')
                        ->orWhere('email','LIKE','%' . $search . '%');
                }
            })
            ->where(function ($query) use ($userRole)
            {
                if($userRole == "Admin")
                {
                    $query->where("role","!=","Super Admin")
                          ->where("role","!=","Admin");
                }
            })
            ->sortable('surname')
            ->paginate($items);
        return $this->test(view('users')->with('data', $users)->with('pagitems', $items)->with('search', $request['search']));
    }

    public function deleteUser(Request $request)
    {

        $user = DB::table('users')
            ->where('id', '=', $request['id'])
            ->update(['deleted_at' => now()]);

        history_action_log::insert(["userID" => Auth::user()->id, "action" => "deleted_user", "forProcessID" => $request['id']]);
        if(!$user)
        {
            return 'User not found';
        }
        /*
        $allocationIds = DB::table('userallocation')
            ->select("allocationid")
            ->where('userID','=', $request['id'])
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
        }*/
        return response()->json("Successfully deleted");
    }

    public function setUserRole(Request $request)
    {
        if($request->id == Auth::user()->id)
        {
            return "false";
        }
        if(Auth::user()->role == "Admin" && ($request->role == "Admin" || $request->role == "Super Admin"))
        {
            return "false";
        }
        if(Auth::user()->role == "Admin" || Auth::user()->role == "Super Admin")
        {
            $user = User::find($request->id);
            $user->role = $request->role;
            $user->save();
            history_action_log::insert(["userID" => Auth::user()->id, "action" => "updated_user_role", "forProcessID" => $request->id]);
            return "success";
        }
        else
        {
            return "unauthorized";
        }
    }

    public function setUserWorkPermit(Request $request)
    {
        if(Auth::user()->role == "Admin" || Auth::user()->role == "Super Admin")
        {
            $user = User::find($request->id);
            $user->canIssueWorkPermit = ($request->canIssueWorkPermit == 'true') ? 1 : 0;
            $user->save();
            history_action_log::insert(["userID" => Auth::user()->id, "action" => "updated_work_permission_user", "forProcessID" => $request->id]);
            return "success";
        }
        else
        {
            return "unauthorized";
        }
    }
    public function setUserEntryPermit(Request $request)
    {
        if(Auth::user()->role == "Admin" || Auth::user()->role == "Super Admin")
        {
            $user = User::find($request->id);
            $user->canIssueEntryPermit = ($request->canIssueEntryPermit == 'true') ? 1 : 0;
            $user->save();
            history_action_log::insert(["userID" => Auth::user()->id, "action" => "updated_entry_permission_user", "forProcessID" => $request->id]);
            return "success";
        }
        else
        {
            return "unauthorized";
        }
    }

}
