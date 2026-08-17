<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class PackageRequestConfirmationMail extends Mailable
{
    public function __construct(public array $requestData) {}

    public function build()
    {
        return $this->subject('Confirm your package request change')->view(
            'emails.package-request-confirmation',
        );
    }
}
