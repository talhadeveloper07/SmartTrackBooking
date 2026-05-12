<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

     public $business;
    public $plan;
    public $amount;

    public function __construct($business, $plan, $amount)
    {
        $this->business = $business;
        $this->plan = $plan;
        $this->amount = $amount;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Invoice',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.org_business.invoice',
        );
    }
}
