<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ServiceRequestRejectedMail extends Mailable
{
    public function __construct(
        public array $requestData
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Request Has Been Rejected'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.service-request-rejected'
        );
    }

    public function attachments(): array
    {
        return [];
    }
}