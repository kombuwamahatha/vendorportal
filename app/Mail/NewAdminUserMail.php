<?php

namespace App\Mail;

use App\Models\AdminUser;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewAdminUserMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public AdminUser $adminUser,
        public string $plainPassword,
        public string $role,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Earthy Ceylon Vendor Portal — Your Admin Account',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.admin.new-user',
        );
    }
}