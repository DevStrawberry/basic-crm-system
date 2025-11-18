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
    private $pdfContent;

    /**
     * Create a new message instance.
     * Recebe o conteúdo do PDF em memória (string binária) para não precisar salvar em disco antes.
     */
    public function __construct(Proposal $proposal, $pdfContent)
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
            view: 'emails.proposal', // View simples com o texto do email
            with: ['proposal' => $this->proposal],
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromData(fn () => $this->pdfContent, 'Proposta_' . $this->proposal->id . '.pdf')
                ->withMime('application/pdf'),
        ];
    }
}