<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use App\Models\CompetitionApplication;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Support\Facades\Storage;

class TestMail extends Mailable
{
    use Queueable, SerializesModels;
    public $mailData;
    /**
     * Create a new message instance.
     */
    public function __construct($mailData)
    {
        $this->mailData = $mailData;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->mailData['subject'],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: $this->mailData['view_name'],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments(): array
    {
        // dd(Attachment::fromStorage($avatarPath));
        // if (Storage::exists($avatarPath)) {
        //     dd('aaa');
        // }

        // if (!is_readable($avatarPath)) {
        //     dd('bbb');
        // }
        return [
            Attachment::fromStorage($this->mailData['file_path']),
        ];
    }
}
