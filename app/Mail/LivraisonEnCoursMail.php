<?php

namespace App\Mail;

use App\Models\Livraison;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LivraisonEnCoursMail extends Mailable
{
    use Queueable, SerializesModels;

    public Livraison $livraison;

    public function __construct(Livraison $livraison)
    {
        $this->livraison = $livraison;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🚚 Votre livraison est en cours — ' .
                     $this->livraison->vente->numero_vente,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.livraison-en-cours',
            with: [
                'livraison' => $this->livraison,
                'client'    => $this->livraison->client,
                'vente'     => $this->livraison->vente,
            ],
        );
    }
}