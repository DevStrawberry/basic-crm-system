<?php

namespace App\Mail;

use App\Models\Contract;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContractSignedMail extends Mailable
{
    use Queueable, SerializesModels;

    private Contract $contract;
    private ?string $pdfPath;

    /**
     * Create a new message instance.
     */
    public function __construct(Contract $contract, ?string $pdfPath = null)
    {
        $this->contract = $contract;
        $this->pdfPath = $pdfPath;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Contrato #' . $this->contract->contract_number . ' - Assinado',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.contract-signed',
            with: [
                'contract' => $this->contract,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $attachments = [];

        if ($this->pdfPath && file_exists(storage_path('app/public/' . $this->pdfPath))) {
            $attachments[] = Attachment::fromPath(storage_path('app/public/' . $this->pdfPath))
                ->as('contrato_' . $this->contract->contract_number . '.pdf');
        }

        return $attachments;
    }
}
