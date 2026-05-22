<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VendorOrderNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $vendor;

    public function __construct($order, $vendor)
    {
        $this->order = $order;
        $this->vendor = $vendor;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🛍️ New order received — ShopLocal (#'.$this->order->id.')',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.vendor_notification',
        );
    }
}