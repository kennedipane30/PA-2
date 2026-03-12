<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendOTPMail extends Mailable
{
    use Queueable, SerializesModels;

    // Properti harus didefinisikan di sini. 
    // Properti publik otomatis tersedia di file Blade (view).
    public $otp;

    /**
     * Membuat instance pesan baru.
     */
    public function __construct($otp)
    {
        $this->otp = $otp;
    }

    /**
     * Mengatur amplop email (Subjek).
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Kode OTP Spekta Academy',
        );
    }

    /**
     * Mengatur isi konten email (View).
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.otp', // Pastikan file ada di resources/views/emails/otp.blade.php
        );
    }

    /**
     * Mengatur lampiran jika ada.
     */
    public function attachments(): array
    {
        return [];
    }
}