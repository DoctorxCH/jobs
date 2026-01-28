<?php

namespace App\Mail;

use App\Models\CompanyInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CompanyInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public CompanyInvitation $invite)
    {
    }

    public function build()
    {
        return $this->subject('Company invitation')
            ->view('mails.company-invitation', [
                'invite' => $this->invite,
            ]);
    }
}
