<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BusinessRegisteredMail extends Mailable
{
    use Queueable, SerializesModels;

     public $business;
    public $user;

    public function __construct($business, $user)
    {
        $this->business = $business;
        $this->user = $user;
    }

    public function build()
    {
        return $this->subject('New Business Registered')
            ->view('emails.business_registered');
    }
    
}
