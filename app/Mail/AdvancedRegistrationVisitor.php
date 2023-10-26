<?php

namespace App\Mail;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AdvancedRegistrationVisitor extends Mailable
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
    private $roadmap;
    private $hygieneRegulations;
    private $GroupMembers;
    private $hygieneRegulationsLanguage;
    private $reasonForVisit;
    private $request;
    public $tries = 2;
    public $retriesIn = 1;

    public function __construct($nam, $cont, $names, $rmap, $hyge, $visiSubs,$req)
    {
        $this->tries = env('MAIL_RETRY');
        $this->retriesIn = env('MAIL_RETRY_DELAY');
        $this->request = $req;
        $this->reasonForVisit = $req['reasonForVisit'];
        $this->name = $nam;
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
            $this->employee = $req['employee'];
        }
        $this->startDate = $req['startDate'];
        $this->endDate = $req['endDate'];
        $this->visitId = $req['visitId'];
        $this->content = $cont;
        $this->roadmap = $rmap;
        $this->hygieneRegulations = $hyge;
        $this->hygieneRegulationsLanguage = "Hygienevorschriften Fremdfirmen -Englisch.pdf";
        $this->GroupMembers = $visiSubs;
        if($nam->language == "german")
        {
            $this->hygieneRegulationsLanguage = "Hygienevorschriften Fremdfirmen -Deutsch.pdf";
            if($nam->salutation == 'Herr')
            {
                $dear = 'geehrter';
            }
            else
            {
                $dear = 'geehrte';
            }
        }
        else
        {
            if($nam->salutation == 'Herr')
            {
                $dear = 'Dear';
            }
            else
            {
                $dear = 'Dear';
            }
        }
        $qrlist = '<dl>';
        $qrlist = $qrlist . '<dt>' . $nam->forename . ' ' . $nam->surname . ' ' . $this->visitId . '-' . $nam->id . '-1</dt><dd><img src="data:image/png;base64,' . base64_encode(QrCode::format('png')->size(100)->generate($this->visitId . '-' . $nam->id . '-1#')) . '"></dd>';

        if(!empty($this->GroupMembers))
        {
            foreach($this->GroupMembers as $groupMember)
            {
                $qrlist = $qrlist . '<dt>' . $groupMember['forename'] . ' ' . $groupMember['surname'] . ' ' . $this->visitId . '-' . $groupMember['id'] . '-0</dt><dd><img src="data:image/png;base64,' . base64_encode(QrCode::format('png')->size(100)->generate($this->visitId . '-' . $groupMember['id'] . '-0#')) . '"></dd>';
            }
        }
        $qrlist = $qrlist . '</dl>';
        $toReplace =
        [
            'visitor.salutation' => $nam->salutation,
            'visitor.forename' => $nam->forename,
            'visitor.surname' => $nam->surname,
            'visitor.title' => $nam->title,
            'startDate' => date('d.m.Y', strtotime($this->startDate)),
            'startTime' => date('H:i', strtotime($this->startDate)),
            'employee.name' => $this->employee,
            'endDate' => date('d.m.Y', strtotime($this->endDate)),
            'endTime' => date('H:i', strtotime($this->endDate)),
            'visitID' => $this->visitId . "-" . $nam->id . "-1",
            'dear' => $dear,
            'employee.mobileNumber' => Auth::user()->mobile_number,
            'employee.landLineNumber' => Auth::user()->telephone_number,
            'employee.department' => Auth::user()->department,
            'employee.email' => Auth::user()->email,
            'visitor.qrlist' => $qrlist,
            'reasonForVisit' => $this->reasonForVisit,

        ];
        if($nam->language != "german")
        {
            if($nam->salutation == "Herr")
            {
                $toReplace['visitor.salutation'] = "Sir";
            }
            else
            {
                $toReplace['visitor.salutation'] = "Madam";
            }
        }
        foreach($toReplace as $itemToReplace => $itemReplace)
        {
            $this->content = str_replace($itemToReplace, $itemReplace, $this->content);
        }
        while(strpos($this->content, 'QR(') !== false)
        {
            $tosearch = substr($this->content, strpos($this->content, 'QR('), strpos($this->content, ')', strpos( $this->content, 'QR(')) - (strpos($this->content, 'QR(') - 1));
            $replacewith = '<img src="data:image/png;base64,' . base64_encode(QrCode::format("png")->size(100)->generate(substr($this->content, strpos($this->content, 'QR(') + 3, strpos($this->content, ')', strpos($this->content, 'QR(') + 3) - (strpos($this->content, 'QR(') + 3)) . "#")) . ' ">';
            $this->content = str_replace($tosearch, $replacewith, $this->content);
        }
        while(strpos($this->content, 'visitor.list') !== false)
        {
            $tosearch = 'visitor.list';
            $replacewith = '<ol>';
            foreach($names as $na)
            {
                $replacewith = $replacewith . '<li>' . $na->salutation . ' ' . $na->forename . ' ' . $na->surname . ' ' . '</li>';
            }
            $replacewith = $replacewith . '</ol>';
            $this->content = str_replace($tosearch, $replacewith, $this->content);
        }

        $file = public_path() . '\\' . "mails\\" . $req['employee'] . " - " . (string)date('Y-m-d H-i',strtotime($req['startDate'])) . '.ics';
        $fileToPut = public_path() . '\\' . "mails\\" . $nam->forename . " - " . $nam->surname . " - " . (string)date('Y-m-d H-i',strtotime($req['startDate'])) . '.ics';
        if(file_exists($file))
        {
            Log::info("ICS-Datei wurde für den Besucher {$toReplace['visitor.forename']} {$toReplace['visitor.surname']} mit der BesuchsID {$toReplace['visitID']} gefunden.");
            $fileContent = file_get_contents($file);
            $fileContent = str_replace("PARAMETER_DESCRIPTION", $this->content, $fileContent);
            $fileChanged = file_put_contents($fileToPut, $fileContent);
        }
        else
        {
            Log::info("ICS-Datei wurde für den Besucher {$toReplace['visitor.forename']} {$toReplace['visitor.surname']} mit der BesuchsID {$toReplace['visitID']} nicht gefunden.");
        }

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if($this->roadmap == 0)
        {
            if($this->hygieneRegulations == 1)
            {
                return $this->subject(env('MAIL_VISITOR_SUBJECT', "Angestellten E-Mail"))
                    ->from(env('MAIL_USERNAME'), 'Heppenheim Visit')
                    ->view('mail.advancedRegistration')
                    ->with([
                        "content" =>    $this->content,
                    ])
                    ->attach(public_path() . '\\' . 'documents\Anfahrskizze Unilever Heppenheim.pdf', [
                        'as' => 'Anfahrtsskizze.pdf',
                        'mime' => 'application/pdf',
                    ])
                    ->attach(public_path() . '\\' . 'documents\\' . $this->hygieneRegulationsLanguage, [
                        'as' => 'Hygienevorschriften.pdf',
                        'mime' => 'application/pdf',
                    ])
                    ->attach(public_path() . '\\' . "mails\\" . $this->name->forename . " - " . $this->name->surname .  " - " . (string)date('Y-m-d H-i', strtotime($this->startDate)) . '.ics', [
                        'as' => 'Unilever-Besuch ' . date('d.m.Y', strtotime($this->startDate)) . ' - ' . date('d.m.Y', strtotime($this->endDate)) . '.ics',
                        'mime' => 'text/calendar;charset=UTF-8;method=REQUEST',
                    ]);
            }
            else
            {
                return $this->subject(env('MAIL_VISITOR_SUBJECT', "Angestellten E-Mail"))
                    ->from(env('MAIL_USERNAME'), 'Heppenheim Visit')
                    ->view('mail.advancedRegistration')
                    ->with([
                        "content" =>    $this->content,
                    ])
                    ->attach(public_path() . '\\' . 'documents\Anfahrskizze Unilever Heppenheim.pdf', [
                        'as' => 'Anfahrtsskizze.pdf',
                        'mime' => 'application/pdf',
                    ])
                    ->attach(public_path() . '\\' . "mails\\" . $this->name->forename . " - " . $this->name->surname . " - " . (string)date('Y-m-d H-i', strtotime($this->startDate)) . '.ics', [
                        'as' => 'Unilever-Besuch ' . date('d.m.Y', strtotime($this->startDate)) . ' - ' . date('d.m.Y', strtotime($this->endDate)) . '.ics',
                        'mime' => 'text/calendar;charset=UTF-8;method=REQUEST',
                    ]);
            }
        }
        else
        {
            if($this->hygieneRegulations == 1)
            {
                return $this->subject(env('MAIL_VISITOR_SUBJECT', "Angestellten E-Mail"))
                    ->from(env('MAIL_USERNAME'), 'Heppenheim Visit')
                    ->view('mail.advancedRegistration')
                    ->with([
                        "content" =>    $this->content,
                    ])
                    ->attach(public_path() . '\\' . 'documents\\' . $this->hygieneRegulationsLanguage, [
                        'as' => 'Hygienevorschriften.pdf',
                        'mime' => 'application/pdf',
                    ])
                    ->attach(public_path() . '\\' . "mails\\" . $this->name->forename . " - " . $this->name->surname . " - " . (string)date('Y-m-d H-i', strtotime($this->startDate)) . '.ics', [
                        'as' => 'Unilever-Besuch ' . date('d.m.Y', strtotime($this->startDate)) . ' - ' . date('d.m.Y', strtotime($this->endDate)) . '.ics',
                        'mime' => 'text/calendar;charset=UTF-8;method=REQUEST',
                    ]);
            }
            else
            {
                return $this->subject(env('MAIL_VISITOR_SUBJECT', "Angestellten E-Mail"))
                    ->from(env('MAIL_USERNAME'), 'Heppenheim Visit')
                    ->view('mail.advancedRegistration')
                    ->with([
                        "content" =>    $this->content,
                    ])
                    ->attach(public_path() . '\\' . "mails\\" . $this->name->forename . " - " . $this->name->surname . " - " . (string)date('Y-m-d H-i', strtotime($this->startDate)) . '.ics', [
                        'as' => 'Unilever-Besuch ' . date('d.m.Y', strtotime($this->startDate)) . ' - ' . date('d.m.Y', strtotime($this->endDate)) . '.ics',
                        'mime' => 'text/calendar;charset=UTF-8;method=REQUEST',
                    ]);
            }
        }
    }
}
