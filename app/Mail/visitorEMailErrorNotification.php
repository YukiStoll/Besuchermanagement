<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class visitorEMailErrorNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $tries = 2;
    public $retryAfter = 1;
    private $content;
    public function __construct($content, $visitorEMail, $errorMsg)
    {
        $this->content = $content;
        $this->tries = env('MAIL_RETRY');
        $this->retryAfter = env('MAIL_RETRY_DELAY');

        $toReplace =
            [
                'visitor.email' => $visitorEMail,
                'errorMsg' => $errorMsg,
            ];
        foreach($toReplace as $itemToReplace => $itemReplace)
        {
            $this->content = str_replace($itemToReplace, $itemReplace, $this->content);
        }
    }

    public function build()
    {
        return $this->subject(env('MAIL_VISITOR_ERROR_NOTIFICATION', "Error Fehler Achtung"))
            ->view('mail.advancedRegistration')
            ->with([
                "content" => $this->content,
            ]);
    }
}
