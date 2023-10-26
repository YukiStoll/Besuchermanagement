<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class visitorArrivalNotice extends Mailable
{
    use Queueable, SerializesModels;

    private $visitId;
    private $employee;
    private $name;
    private $content;
    private $visit;
    public $tries = 2;
    public $retryAfter = 1;

    public function __construct($visitor, $vid, $emplo, $cont, $visit, $visitors)
    {

        $this->tries = env('MAIL_RETRY');
        $this->retryAfter = env('MAIL_RETRY_DELAY');
        $this->visit = $visit;
        $this->employee = $emplo;
        $this->visitId = $vid;
        $this->content = $cont;
        if(isset($visitor->salutation))
        {
            $salutation = $visitor->salutation;
        }
        else
        {
            $salutation = "";
        }
        if(isset($visitor->title))
        {
            $title = $visitor->title;
        }
        else
        {
            $title = "";
        }
        if(isset($visitor->salutation) && $visitor->salutation == 'Herr')
        {
            $dear = 'geehrter';
        }
        else if(isset($visitor->salutation))
        {
            $dear = 'geehrte';
        }
        else
        {
            $dear = "geehrte/r";
        }

        $visitorList = "";
        $countt = 0;
        foreach($visitors as $na)
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
                'reasonForVisit' => $this->visit['reasonForVisit'],
                'startDate' => date('d.m.Y', strtotime($this->visit['startDate'])),
                'startTime' => date('H:i', strtotime($this->visit['startDate'])),
                'endDate' => date('d.m.Y', strtotime($this->visit['endDate'])),
                'endTime' => date('H:i', strtotime($this->visit['endDate'])),
                'visitor.salutation' => $salutation,
                'visitor.forename' => $visitor->forename,
                'visitor.surname' => $visitor->surname,
                'visitor.title' => $title,
                'visitor.list' => $visitorList,
                'employee.name' => $this->employee->name,
                'visitID' => $this->visitId,
                'dear' => $dear,
                'employee.mobileNumber' => $this->employee->mobile_number,
                'employee.landLineNumber' => $this->employee->telephone_number,
                'employee.department' => $this->employee->department,
                'employee.email' => $this->employee->email,

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
    }

    public function build()
    {
        return $this->subject(env('MAIL_VISITOR_ARRIVAL_NOTICE_SUBJECT', "Ihr Besucher ist angekommen"))
            ->view('mail.advancedRegistration')
            ->with([
                "content" =>    $this->content,
            ]);
    }
}
