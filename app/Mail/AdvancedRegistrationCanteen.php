<?php

namespace App\Mail;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AdvancedRegistrationCanteen extends Mailable
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
    private $content;
    private $canteenIds;
    private $canteenVisitorNumber;
    private $reasonForVisit;
    private $GroupMembers;
    public $tries = 2;
    public $retriesIn = 1;

    public function __construct($sDate, $eDate, $vid, $emplo, $nam, $cont, $cIds, $reasonForVisit, $request, $groupNames = null)
    {
        $this->tries = env('MAIL_RETRY');
        $this->retriesIn = env('MAIL_RETRY_DELAY');
        $this->name = $nam;
        $this->GroupMembers = $groupNames;
        $department = "";
        if(isset($request['userids']) && count($request['userids']) > 1)
        {
            $employees = User::select("forename", "surname", "department")->whereIn("id", $request['userids'])->where("id", "!=", Auth::user()->id)->get();
            if(count($employees) > 1)
            {
                $count = 0;
                foreach($employees as $employee)
                {
                    if($department == "")
                    {
                        $department = $employee->department;
                    }
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
            $this->employee = $emplo;
        }

        if($department == "")
        {
            $department = Auth::user()->department;
        }



        $this->startDate = $sDate;
        $this->endDate = $eDate;
        $this->visitId = $vid;
        $this->content = $cont;
        $this->canteenIds = $cIds;
        $this->reasonForVisit = $reasonForVisit;
        for($i = 0; $i < sizeof($nam); $i++)
        {
            if(isset($nam[$i]->salutation) && (array)$nam[$i]->salutation == 'Herr')
            {
                $dear[$i] = 'geehrter';
            }
            else if(isset($nam[$i]->salutation))
            {
                $dear[$i] = 'geehrte';
            }
            else
            {
                $dear[$i] = 'geehrte/r';
            }
        }

        while(strpos($this->content, 'canteen.list') !== false)
        {
            $tosearch = 'canteen.list';
            $replacewith = '<ol>';
            foreach($nam as $na)
            {
                if($na != null)
                {
                    foreach ($this->canteenIds as $canId)
                    {
                        if($na->id == $canId)
                        {
                            $this->canteenVisitorNumber = $this->canteenVisitorNumber + 1;
                            $replacewith = $replacewith . '<li>' . $na->salutation . ' ' . $na->title . ' ' . $na->forename . ' ' . $na->surname . ' ' . '</li>';
                        }
                    }
                }
            }
            if(!empty($this->GroupMembers))
            {
                foreach($this->GroupMembers as $groupMember)
                {
                    $this->canteenVisitorNumber = $this->canteenVisitorNumber + 1;
                    $replacewith = $replacewith . '<li>' . $groupMember['forename'] . ' ' . $groupMember['surname'] . ' ' . '</li>';
                }
            }
            $replacewith = $replacewith . '</ol>';
            $this->content = str_replace($tosearch, $replacewith, $this->content);
        }

        $toReplace =
        [

            'visitor.salutation' => $nam[0]->salutation,
            'visitor.forename' => $nam[0]->forename,
            'visitor.surname' => $nam[0]->surname,
            'visitor.title' => $nam[0]->title,
            'startDate' => date('d.m.Y', strtotime($this->startDate)),
            'startTime' => date('H:i', strtotime($this->startDate)),
            'employee.name' => $this->employee,
            'endDate' => date('d.m.Y', strtotime($this->endDate)),
            'endTime' => date('H:i', strtotime($this->endDate)),
            'visitID' => $this->visitId,
            'dear' => $dear[0],
            'employee.mobileNumber' => Auth::user()->mobile_number,
            'employee.landLineNumber' => Auth::user()->telephone_number,
            'employee.department' => $department,
            'employee.email' => Auth::user()->email,
            'canteen.number' => $this->canteenVisitorNumber,
            'reasonForVisit' => $this->reasonForVisit,

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

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject(env('MAIL_CANTEEN_SUBJECT', "Kantinen E-Mail"))
        ->view('mail.advancedRegistration')
        ->with([
            "content" =>    $this->content,
        ]);
    }

}
