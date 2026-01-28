<?php

namespace App\Mail;

use App\Models\CompanyInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CompanyInviteMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public CompanyInvitation $invite)
    {
    }

    public function build()
    {
        return $this->subject('You have been invited to join a company')
            ->view('mails.company-invite')
            ->with([
                'invite' => $this->invite,
                'acceptUrl' => route('company.invite.accept', ['token' => $this->invite->token]),
            ]);
    }
}
