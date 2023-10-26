<?php

namespace App\Mail;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AdvancedRegistrationEmployee extends Mailable
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
    private $creator;
    private $name;
    private $content;
    private $reasonForVisit;
    public $tries = 2;
    public $retriesIn = 1;

    public function __construct($sDate, $eDate, $vid, $emplo, $nam, $cont, $visiSubs, $reason, $req)
    {
        $this->tries = env('MAIL_RETRY');
        $this->retriesIn = env('MAIL_RETRY_DELAY');
        $this->reasonForVisit = $reason;
        $this->name = new \stdClass();
        $realKey = 0;
        foreach ($nam as $key => $values)
        {
            $object = new \stdClass();
            if($values != null)
            {
                foreach ($values as $key_val => $value)
                {
                    $object->$key_val = $value;
                }
                $key = "a" . $key;
                $this->name->$key = $object;
            }
        }
        if(isset($req['userids']) && count($req['userids']) > 1)
        {
            $employees = User::select("forename", "surname")->whereIn("id", $req['userids'])->where("id", "!=", Auth::user()->id)->get();
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
            $this->employee = $emplo;
        }
        $this->creator = $emplo;
        $this->startDate = $sDate;
        $this->endDate = $eDate;
        $this->visitId = $vid;
        $this->content = $cont;
        $this->GroupMembers = $visiSubs;
        if($this->name->a0->salutation == 'Herr')
        {
            $dear = 'geehrter';
        }
        else
        {
            $dear = 'geehrte';
        }
        $qrlist = "";
        if(!empty($this->name))
        {
            $qrlist = '<dl>';
            foreach($this->name as $na)
            {
                if (isset($na->title))
                {
                    $title =  $na->title . ' ';
                }
                else
                {
                    $title = '';
                }
                if(isset($na->salutation))
                {
                    $salutation = $na->salutation;
                }
                else
                {
                    $salutation = '';
                }
                $qrlist = $qrlist . '<dt>' . $salutation . ' ' . $title . $na->forename . ' ' . $na->surname . ' ' . $this->visitId . '-' . $na->id . '-1</dt><dd><img src="data:image/png;base64,' . base64_encode(QrCode::format('png')->size(100)->generate($this->visitId . '-' . $na->id . '-1#')) . '"></dd>';
                $title = "";
            }
            if(!empty($this->GroupMembers))
            {
                foreach($this->GroupMembers as $groupMember)
                {
                    $qrlist = $qrlist . '<dt>' . $groupMember['forename'] . ' ' . $groupMember['surname'] . ' ' . $this->visitId . '-' . $groupMember['id'] . '-0</dt><dd><img src="data:image/png;base64,' . base64_encode(QrCode::format('png')->size(100)->generate($this->visitId . '-' . $groupMember['id'] . '-0#')) . '"></dd>';
                }
                $qrlist = $qrlist . '</dl>';
            }
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
                'visitor.salutation' => $this->name->a0->salutation,
                'visitor.forename' => $this->name->a0->forename,
                'visitor.surname' => $this->name->a0->surname,
                'visitor.title' => $this->name->a0->title,
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
                'visitor.qrlist' => $qrlist,
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
            foreach($this->name as $na)
            {
                $replacewith = $replacewith . '<li>' . $na->salutation . ' ' . $na->forename . ' ' . $na->surname . ' ' . '</li>';
            }
            $replacewith = $replacewith . '</ol>';
            $this->content = str_replace($tosearch, $replacewith, $this->content);
        }
        $file = public_path() . '\\' . "mails\\" . $this->creator . " - " . (string)date('Y-m-d H-i',strtotime($this->startDate)) . '.ics';
        $fileToPut = public_path() . '\\' . "mails\\" . $this->creator . " - " . (string)date('Y-m-d H-i',strtotime($this->startDate)) . " - " . (string)date('Y-m-d H-i',strtotime($this->endDate)) . '.ics';
        if(file_exists($file))
        {
            Log::info("ICS-Datei wurde für den Angestellten {$toReplace['employee.name']} mit der BesuchsID {$toReplace['visitID']} gefunden.");
            $fileContent = file_get_contents($file);
            $fileContent = str_replace("PARAMETER_DESCRIPTION", $this->content, $fileContent);
            $fileChanged = file_put_contents($fileToPut, $fileContent);
        }
        else
        {
            Log::info("ICS-Datei wurde für den Angestellten {$toReplace['employee.name']} mit der BesuchsID {$toReplace['visitID']} nicht gefunden.");
        }

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $file = public_path() . '\\' . "mails\\" . $this->creator . " - " . (string)date('Y-m-d H-i',strtotime($this->startDate)) . " - " . (string)date('Y-m-d H-i',strtotime($this->endDate)) . '.ics';
        return $this->subject(env('MAIL_EMPLOYEE_SUBJECT', "Besucher E-Mail"))
            ->view('mail.advancedRegistration')
            ->with([
                "content" =>    $this->content,
            ])
            ->attach($file, [
                'as' => 'Unilever-Besuch ' . date('d.m.Y', strtotime($this->startDate)) . ' - ' . date('d.m.Y', strtotime($this->endDate)) . '.ics',
                'mime' => 'text/calendar;charset=UTF-8;method=REQUEST',
            ]);
    }

}
