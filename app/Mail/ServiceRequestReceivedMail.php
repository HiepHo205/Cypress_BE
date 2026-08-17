<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ServiceRequestReceivedMail extends Mailable
{
    public function __construct(
        public array $requestData
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'We Received Your Request'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.service-request-received'
        );
    }

    public function attachments(): array
    {
        return [];
    }
}