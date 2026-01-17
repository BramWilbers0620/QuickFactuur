<?php

namespace App\Mail;

use App\Models\Quote;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class QuoteMail extends Mailable
{
    use Queueable, SerializesModels;

    public Quote $quote;

    /**
     * Create a new message instance.
     */
    public function __construct(Quote $quote)
    {
        $this->quote = $quote;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Offerte ' . $this->quote->quote_number . ' van ' . $this->quote->company_name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.quote',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        if ($this->quote->pdf_path && Storage::disk('local')->exists($this->quote->pdf_path)) {
            return [
                Attachment::fromStorage($this->quote->pdf_path)
                    ->as('offerte-' . $this->quote->quote_number . '.pdf')
                    ->withMime('application/pdf'),
            ];
        }

        return [];
    }
}
