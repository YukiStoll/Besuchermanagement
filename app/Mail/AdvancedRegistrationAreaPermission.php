<?php

namespace App\Mail;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Http\File;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AdvancedRegistrationAreaPermission extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    private $startDate;
    private $endDate;
    private $visitId;
    private $employee;
    private $name;
    private $areaPermissionName;
    private $areaPermissionID;
    private $content;
    private $reasonForVisit;
    private $permissiontype;
    private $workPermissionApprovalText;
    private $entryPermissionText;
    public $tries = 2;
    public $retriesIn = 1;

    public function __construct($nam, $cont, $request)
    {
        $this->tries = env('MAIL_RETRY');
        $this->retriesIn = env('MAIL_RETRY_DELAY');
        $this->reasonForVisit = $request['reasonForVisit'];
        $this->name = $nam;
        if(isset($request['userids']) && count($request['userids']) > 1)
        {
            $employees = User::select("forename", "surname")->whereIn("id", $request['userids'])->where("id", "!=", Auth::user()->id)->get();
            if(count($employees) > 1)
            {
                $count = 0;
                foreach($employees as $employee)
                {
                    if($count != 0)
                    {
                        $this->employee = $employee->forename . " " . $employee->surname . ", " . $this->employee;
                    }
                    else
                    {
                        $count = 1;
                        $this->employee = $employee->forename . " " . $employee->surname . $this->employee;
                    }
                }
            }
            else
            {
                $this->employee = $employees[0]->forename . " " . $employees[0]->surname;
            }
        }
        else
        {
            $this->employee = $request['employee'];
        }
        $this->startDate = $request['startDate'];
        $this->endDate = $request['endDate'];
        $this->visitId = $request['visitId'];
        $this->areaPermissionName = $request['areaPermissionName'];
        $this->areaPermissionID = $request['areaPermissionID'];
        $this->content = $cont;
        if($nam[0]->salutation == 'Herr')
        {
            $dear = 'geehrter';
        }
        else
        {
            $dear = 'geehrte';
        }
        $permission = strpos($this->content, 'workPermission.Link');
        if($permission)
        {
            $permissionType = "Arbeitserlaubnis";
            $this->permissiontype = 1;
        }
        else
        {
            $permissionType = "Einfahrtsgenehmigung";
            $this->permissiontype = 2;
        }

        $visitorList = "";
        $countt = 0;
        Log::debug($nam);
        foreach($nam as $na)
        {
            if($countt != 0)
            {
                $visitorList = $na->forename . " " . $na->surname . ", " . $visitorList;
            }
            else
            {
                $countt = 1;
                $visitorList = $na->forename . " " . $na->surname . $visitorList;
            }
        }

        $toReplace =
        [

            'visitor.salutation' => $nam[0]->salutation,
            'visitor.forename' => $nam[0]->forename,
            'visitor.surname' => $nam[0]->surname,
            'visitor.title' => $nam[0]->title,
            'visitor.list' => $visitorList,
            'startDate' => date('d.m.Y', strtotime($this->startDate)),
            'startTime' => date('H:i', strtotime($this->startDate)),
            'employee.name' => $this->employee,
            'endDate' => date('d.m.Y', strtotime($this->endDate)),
            'endTime' => date('H:i', strtotime($this->endDate)),
            'visitID' => $this->visitId,
            'dear' => $dear[0],
            'employee.mobileNumber' => Auth::user()->mobile_number,
            'employee.landLineNumber' => Auth::user()->telephone_number,
            'employee.department' => Auth::user()->department,
            'employee.email' => Auth::user()->email,
            'areaPermission.Link' => "<div>
                                        <table cellspacing='0' cellpadding='0'>
                                            <tr>
                                                <td align='center' width='300' height='40' bgcolor='#232F85' style='-webkit-border-radius: 5px; -moz-border-radius: 5px; border-radius: 5px; color: #ffffff; display: block;'>
                                                    <a href=" . route('areaPermission.email', $this->visitId) . "?status=granted&id=" . $this->areaPermissionID . " style='font-size:14px; font-weight: bold; font-family:sans-serif; text-decoration: none; line-height:40px; width:100%; display:inline-block'>
                                                        <span style='color: #ffffff;'>
                                                            Zutrittssgenehmigung erteilen
                                                        </span>
                                                    </a>
                                                </td>
                                                <td align='center' width='300' height='40' bgcolor='#232F85' style='-webkit-border-radius: 5px; -moz-border-radius: 5px; border-radius: 5px; color: #ffffff; display: block;'>
                                                    <a href=" . route('areaPermission.email', $this->visitId) . "?status=denied&id=" . $this->areaPermissionID . " style='font-size:14px; font-weight: bold; font-family:sans-serif; text-decoration: none; line-height:40px; width:100%; display:inline-block'>
                                                        <span style='color: #ffffff;'>
                                                            Zutrittssgenehmigung verweigern
                                                        </span>
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>",
            'reasonForVisit' => $this->reasonForVisit,
            'areaPermission.name' => $this->areaPermissionName,

        ];
        foreach($toReplace as $itemToReplace => $itemReplace)
        {
            $this->content = str_replace($itemToReplace, $itemReplace, $this->content);
        }
        while(strpos($this->content, 'QR(') !== false)
        {
            $tosearch = substr($this->content, strpos($this->content, 'QR('), strpos($this->content, ')', strpos( $this->content, 'QR(')) - (strpos($this->content, 'QR(') - 1));
            $replacewith = '<img src="data:image/png;base64,' . base64_encode(QrCode::format("png")->size(100)->generate(substr($this->content, strpos($this->content, 'QR(') + 3, strpos($this->content, ')', strpos($this->content, 'QR(') + 3) - (strpos($this->content, 'QR(') + 3)))) . ' ">';
            $this->content = str_replace($tosearch, $replacewith, $this->content);
        }
        while(strpos($this->content, 'visitor.list') !== false)
        {
            $tosearch = 'visitor.list';
            $replacewith = '<ol>';
            foreach($nam as $na)
            {
                $replacewith = $replacewith . '<li>' . $na->salutation . ' ' . $na->forename . ' ' . $na->surname . ' ' . '</li>';
            }
            $replacewith = $replacewith . '</ol>';
            $this->content = str_replace($tosearch, $replacewith, $this->content);
        }

    }

    public function build()
    {
        $subject = env('MAIL_AREA_PERMISSION_SUBJECT', "Zutrittsgenehmigungs E-Mail");
        $mail = $this->subject($subject)
            ->view('mail.advancedRegistration')
            ->with([
                "content" =>    $this->content,
            ]);
        return $mail;
    }

}
