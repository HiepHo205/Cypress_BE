<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ServiceRequestApprovedMail extends Mailable
{
    public function __construct(
        public array $requestData
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Request Has Been Approved'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.service-request-approved'
        );
    }

    public function attachments(): array
    {
        return [];
    }
}