<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class PackageRequestApprovedMail extends Mailable
{
    public function __construct(
        public array $requestData
    ) {}

    public function build()
    {
        return $this
            ->subject(
                'Your package request has been approved'
            )
            ->view(
                'emails.package-request-approved'
            );
    }
}