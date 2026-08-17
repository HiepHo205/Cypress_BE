<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class PackageRequestRejectedMail extends Mailable
{
    public function __construct(
        public array $requestData
    ) {}

    public function build()
    {
        return $this
            ->subject(
                'Your package request has been rejected'
            )
            ->view(
                'emails.package-request-rejected'
            );
    }
}