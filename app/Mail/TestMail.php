<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TestMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Build the message.
     */
    public function build(): self
    {
        return $this
            ->subject('PriceBuddy test email')
            ->html(
                '<p>This is a test email from PriceBuddy.</p>'.
                '<p>If you received this, your SMTP settings are working correctly.</p>'
            );
    }
}
