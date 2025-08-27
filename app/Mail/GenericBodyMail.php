<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Symfony\Component\Mime\Email as SymfonyEmail;

class GenericBodyMail extends Mailable
{
    public string $subjectLine;
    public string $bodyHtml;
    public ?string $fromAddress;
    public ?string $fromName;
    public ?string $bccAddress;

    public function __construct(string $subjectLine, string $bodyHtml, ?string $fromAddress = null, ?string $fromName = null, ?string $bccAddress = null)
    {
        $this->subjectLine = $subjectLine;
        $this->bodyHtml    = $bodyHtml;
        $this->fromAddress = $fromAddress ?: config('mail.from.address');
        $this->fromName    = $fromName   ?: config('mail.from.name');
        $this->bccAddress  = $bccAddress;
    }

    public function build()
    {
        // Set From / Reply-To
        if ($this->fromAddress) {
            $this->from($this->fromAddress, $this->fromName)
                 ->replyTo($this->fromAddress, $this->fromName);
        }
        if ($this->bccAddress) {
            $this->bcc($this->bccAddress);
        }

        // HTML body directly
        $this->html($this->bodyHtml);

        // Plain-text alternative via Symfony message (NOT Mailable->text(), which expects a *view* name)
        $textAlt = trim(preg_replace('/\s+/', ' ', strip_tags($this->bodyHtml)));
        if ($textAlt === '') { $textAlt = ' '; }

        $this->withSymfonyMessage(function (SymfonyEmail $message) use ($textAlt) {
            $message->text($textAlt);
        });

        return $this->subject($this->subjectLine);
    }
}
