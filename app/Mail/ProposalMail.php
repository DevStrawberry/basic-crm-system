<?php

namespace App\Mail;

use App\Models\Proposal;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProposalMail extends Mailable
{
    use Queueable, SerializesModels;

    private Proposal $proposal;
    private $pdfContent; // Pode ser null

    // Agora o pdfContent é opcional (= null)
    public function __construct(Proposal $proposal, $pdfContent = null)
    {
        $this->proposal = $proposal;
        $this->pdfContent = $pdfContent;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Proposta Comercial: ' . $this->proposal->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.proposal', 
            with: ['proposal' => $this->proposal],
        );
    }

    public function attachments(): array
    {
        // Se não tiver conteúdo de PDF, não anexa nada
        if (!$this->pdfContent) {
            return [];
        }

        return [
            Attachment::fromData(fn () => $this->pdfContent, 'Proposta_' . $this->proposal->id . '.pdf')
                ->withMime('application/pdf'),
        ];
    }
}