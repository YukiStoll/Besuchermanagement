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
        $admin_settings = admin_setting::all();
        return $this->test(view('adminSettings')->with("admin_setting", $admin_settings));
    }

    public function update(Request $request)
    {

        $workPermissions[] =
            [
                "file" => $request->file('00_Allgemeine_Arbeitserlaubnis'),
                "name" => "00 Allgemeine Arbeitserlaubnis",
            ];
        $workPermissions[] =
            [
                "file" => $request->file('01_spez__Arbeitserlaubnis_Feuer_und_Schweißen'),
                "name" => "01 spez. Arbeitserlaubnis Feuer und Schweißen",
            ];
        $workPermissions[] =
            [
                "file" => $request->file('02_spez__Arbeitserlaubnis_Höhe'),
                "name" => "02 spez. Arbeitserlaubnis Höhe",
            ];
        $workPermissions[] =
            [
                "file" => $request->file('03_spez__Arbeitserlaubnis_Behälter'),
                "name" => "03 spez. Arbeitserlaubnis Behälter",
            ];
        $workPermissions[] =
            [
                "file" => $request->file('04_spez__Arbeitserlaubnis_Erdarbeiten'),
                "name" => "04 spez. Arbeitserlaubnis Erdarbeiten",
            ];
        $workPermissions[] =
            [
                "file" => $request->file('05_spez__Arbeitserlaubnis_Ammoniak'),
                "name" => "05 spez. Arbeitserlaubnis Ammoniak",
            ];
        $workPermissions[] =
            [
                "file" => $request->file('06_spez__Arbeitserlaubnis_Öffnen_von_Systemen'),
                "name" => "06 spez. Arbeitserlaubnis Öffnen von Systemen",
            ];
        $workPermissions[] =
            [
                "file" => $request->file('07_spez__Arbeitserlaubnis_Kran'),
                "name" => "07 spez. Arbeitserlaubnis Kran",
            ];
        $workPermissions[] =
            [
                "file" => $request->file('08_spez__Arbeitserlaubnis_Spannung'),
                "name" => "08 spez. Arbeitserlaubnis Spannung",
            ];
        $workPermissions[] =
            [
                "file" => $request->file('09_spez__Arbeitserlaubnis_Heißwasserkessel'),
                "name" => "09 spez. Arbeitserlaubnis Heißwasserkessel",
            ];
        $workPermissions[] =
            [
                "file" => $request->file('10 spez. Arbeitserlaubnis Gefriertunnel Tippbetrieb'),
                "name" => "10 spez. Arbeitserlaubnis Gefriertunnel Tippbetrieb",
            ];
        $documents[] =
            [
                "file" => $request->file('roadmap'),
                "name" => "Anfahrskizze Unilever Heppenheim",
            ];
        $documents[] =
            [
                "file" => $request->file('hygieneRegulationsDE'),
                "name" => "Hygienevorschriften Fremdfirmen -Deutsch",
            ];
        $documents[] =
            [
                "file" => $request->file('hygieneRegulationsENG'),
                "name" => "Hygienevorschriften Fremdfirmen -Englisch",
            ];
        $hasWorkpermissin = false;
        foreach ($workPermissions as $workPermission)
        {
            if($workPermission['file'] != null && $workPermission['file']->isValid() && $workPermission['file']->getClientMimeType() == 'application/pdf')
            {
                $hasWorkpermissin = true;
                $names[] = $workPermission['name'];
                $workPermission['file']->move("workPermissionDocuments/documents/", $workPermission['name'] . ".pdf");
            }
        }
        if($hasWorkpermissin)
        {
            history_action_log::insert(["userID" => Auth::user()->id, "action" => "updated_work_permission_documents"]);
        }

        foreach ($documents as $document)
        {
            if($document['file'] != null && $document['file']->isValid() && $document['file']->getClientMimeType() == 'application/pdf')
            {
                $names[] = $document['name'];
                $document['file']->move("documents/", $document['name'] . ".pdf");
            }
        }


        foreach ($request->input() as $key => $input)
        {
            if($key != "_token")
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
