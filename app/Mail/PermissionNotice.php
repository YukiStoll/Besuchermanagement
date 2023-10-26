<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PermissionNotice extends Mailable
{
    use Queueable, SerializesModels;

    private $startDate;
    private $endDate;
    private $visitId;
    private $employee;
    private $name;
    private $content;
    private $reasonForVisit;
    private $permissiontype;
    private $workPermissionApprovalText;
    private $entryPermissionText;
    private $areaPermissionName;
    public $tries = 2;
    public $retriesIn = 1;

    public function __construct($nam, $cont, $data)
    {
        $this->tries = env('MAIL_RETRY');
        $this->retriesIn = env('MAIL_RETRY_DELAY');
        $this->workPermissionApprovalText = $data['workPermissionApprovalText'];
        $this->entryPermissionText = $data['entryPermissionText'];
        $this->name = $nam;
        $this->content = $cont;
        $this->reasonForVisit = $data['reasonForVisit'];
        $this->areaPermissionName = array_key_exists('areaPermissionName', $data) ? $data["areaPermissionName"] : "";
        if($data['permission_type'] == 'work_permission')
        {
            $permissionType = "Arbeitserlaubnis";
            $this->permissiontype = 1;
        }
        else if($data['permission_type'] == 'entry_permission')
        {
            $permissionType = "Einfahrtsgenehmigung";
            $this->permissiontype = 2;
        }
        else if($data['permission_type'] == 'area_permission')
        {
            $permissionType = "Zutrittsgenehmigung";
            $this->permissiontype = 3;
        }

        $visitorList = "";
        $countt = 0;
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
                'startDate' => date('d.m.Y', strtotime($data['startDate'])),
                'startTime' => date('H:i', strtotime($data['startDate'])),
                'endDate' => date('d.m.Y', strtotime($data['endDate'])),
                'endTime' => date('H:i', strtotime($data['endDate'])),
                'visitID' => $this->visitId,
                'employee.name' => $data['employee_name'],
                'employee.mobileNumber' => $data['employee_mobile_number'],
                'employee.landLineNumber' => $data['employee_telephone_number'],
                'employee.department' => $data['employee_department'],
                'employee.email' => $data['employee_email'],
                'approver.name' => $data['approverName'],
                'reasonForVisit' => $this->reasonForVisit,
                'reasonForWorkPermission' => $this->workPermissionApprovalText,
                'reasonForEntryPermission' => $this->entryPermissionText,
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
                $replacewith = $replacewith . '<li>' . $na->salutation . ' ' . $na->title . ' ' . $na->forename . ' ' . $na->surname . ' ' . '</li>';
            }
            $replacewith = $replacewith . '</ol>';
            $this->content = str_replace($tosearch, $replacewith, $this->content);
        }
    }

    public function build()
    {
        if($this->permissiontype == 1)
        {
            $subject = env('MAIL_WORK_PERMISSION_NOTICE', "Arbeitserlaubniszustimmungsbenachrichtigungs E-Mail");
        }
        else if($this->permissiontype == 2)
        {
            $subject = env('MAIL_ENTRY_PERMISSION_NOTICE', "Einfahrtserlaubniszustimmungsbenachrichtigungs E-Mail");
        }
        else if($this->permissiontype == 3)
        {
            $subject = env('MAIL_AREA_PERMISSION_NOTICE', "Zutrittserlaubniszustimmungsbenachrichtigungs E-Mail");
        }
        $mail = $this->subject($subject)
            ->view('mail.advancedRegistration')
            ->with([
                "content" =>    $this->content,
            ]);
        return $mail;
    }
}
