<?php

namespace App\Mail;

use App\Models\CompanyInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CompanyInvitationMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(public CompanyInvitation $invite)
    {
    }

    public function build()
    {
        return $this
            ->subject('You have been invited to join a company')
            ->view('emails.company-invitation', [
                'invite' => $this->invite,
                'company' => $this->invite->company,
                'acceptUrl' => url('/company-invite/' . $this->invite->token),
            ]);
    }
}
