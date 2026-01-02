<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class PaymentReminderMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Invoice $invoice;
    public int $daysOverdue;

    public function __construct(Invoice $invoice, int $daysOverdue)
    {
        $this->invoice = $invoice;
        $this->daysOverdue = $daysOverdue;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Herinnering: Factuur ' . $this->invoice->invoice_number . ' nog niet betaald',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.payment-reminder',
        );
    }

    public function attachments(): array
    {
        $attachments = [];

        if ($this->invoice->pdf_path && Storage::disk('local')->exists($this->invoice->pdf_path)) {
            $attachments[] = Attachment::fromStorageDisk('local', $this->invoice->pdf_path)
                ->as('factuur-' . $this->invoice->invoice_number . '.pdf')
                ->withMime('application/pdf');
        }

        return $attachments;
    }
}
