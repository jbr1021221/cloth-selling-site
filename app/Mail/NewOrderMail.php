<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Mailable sent to the admin whenever a new order is placed.
 *
 * Dispatched from: CheckoutController::place()
 * Recipient:       ADMIN_EMAIL in .env  (via config('services.admin.email'))
 */
class NewOrderMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public readonly Order $order)
    {
        //
    }

    /**
     * Get the message envelope.
     * Subject line shown in the inbox.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "🛒 New Order #{$this->order->order_number} — ClothStore",
        );
    }

    /**
     * Get the message content definition.
     * Points to resources/views/emails/new-order.blade.php
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.new-order',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
