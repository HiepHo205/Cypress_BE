<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class PackageRequestReceivedMail extends Mailable
{
    public function __construct(
        public array $requestData
    ) {}

    public function build()
    {
        return $this
            ->subject(
                'We received your package request'
            )
            ->view(
                'emails.package-request-received'
            );
    }
}