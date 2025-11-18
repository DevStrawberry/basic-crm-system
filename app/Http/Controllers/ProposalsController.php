<?php

namespace App\Http\Controllers;

use App\Mail\ProposalMail;
use App\Models\Lead;
use App\Models\PipelineStage;
use App\Models\Proposal;
use Barryvdh\DomPDF\Facade\Pdf; // Certifique-se de ter o dompdf instalado
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ProposalsController extends Controller
{
    /**
     * Listar propostas de um lead específico.
     */
    public function index(string $leadId)
    {
        $lead = Lead::findOrFail($leadId);
        $proposals = Proposal::with('createdBy')
            ->where('lead_id', $leadId)
            ->latest()
            ->paginate(10);

        return view('proposals.index', compact('lead', 'proposals', 'leadId'));
    }

    /**
     * Formulário de criação.
     */
    public function create(string $leadId)
    {
        return view('proposals.create', compact('leadId'));
    }

    /**
     * Salvar nova proposta.
     */
    public function store(Request $request, string $leadId)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'service_description' => 'required|string',
            'total_value' => 'required|numeric|min:0',
            'valid_until' => 'required|date|after_or_equal:today',
            'warranties' => 'required|string', // Conforme sua migration (not nullable)
            'notes' => 'nullable|string',
        ]);

        try {
            Proposal::create([
                'lead_id' => $leadId,
                'created_by' => Auth::id(), // Campo obrigatório na migration
                'status' => 'Draft', // Default da migration
                ...$validated
            ]);

            return redirect()->route('leads.proposals.index', $leadId)
                ->with('success', 'Proposta gerada com sucesso!');

        } catch (\Exception $e) {
            Log::error('Erro ao criar proposta: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Erro ao salvar proposta.'])->withInput();
        }
    }

    /**
     * Exibir detalhes da proposta.
     */
    public function show(string $leadId, string $proposalId)
    {
        $proposal = Proposal::with(['lead.client', 'createdBy'])
            ->where('lead_id', $leadId)
            ->findOrFail($proposalId);

        return view('proposals.show', compact('proposal', 'leadId'));
    }

    /**
     * Editar proposta (Apenas se status for Draft).
     */
    public function edit(string $leadId, string $proposalId)
    {
        $proposal = Proposal::where('lead_id', $leadId)->findOrFail($proposalId);

        if ($proposal->status !== 'Draft') {
            return back()->with('error', 'Apenas propostas em rascunho (Draft) podem ser editadas.');
        }

        return view('proposals.edit', compact('proposal', 'leadId'));
    }

    /**
     * Atualizar proposta.
     */
    public function update(Request $request, string $leadId, string $proposalId)
    {
        $proposal = Proposal::where('lead_id', $leadId)->findOrFail($proposalId);

        if ($proposal->status !== 'Draft') {
            abort(403, 'Proposta não editável.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'service_description' => 'required|string',
            'total_value' => 'required|numeric|min:0',
            'valid_until' => 'required|date',
            'warranties' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $proposal->update($validated);

        return redirect()->route('leads.proposals.show', [$leadId, $proposalId])
            ->with('success', 'Proposta atualizada com sucesso!');
    }

    /**
     * Deletar (Soft Delete).
     */
    public function destroy(string $leadId, string $proposalId)
    {
        $proposal = Proposal::where('lead_id', $leadId)->findOrFail($proposalId);
        $proposal->delete();

        return redirect()->route('leads.proposals.index', $leadId)
            ->with('success', 'Proposta excluída.');
    }

    /**
     * RF19: Gerar PDF.
     */
    public function generatePdf(string $leadId, string $proposalId)
    {
        $proposal = Proposal::with(['lead.client', 'createdBy'])->where('lead_id', $leadId)->findOrFail($proposalId);
        
        $pdf = Pdf::loadView('proposals.pdf', compact('proposal'));
        
        return $pdf->stream('proposta_' . $proposal->id . '.pdf');
    }

    /**
     * RF20 e RF21: Enviar por E-mail e Atualizar Status.
     */
    public function sendEmail(string $leadId, string $proposalId)
    {
        $proposal = Proposal::with(['lead.client'])->where('lead_id', $leadId)->findOrFail($proposalId);
        $clientEmail = $proposal->lead->client->email;

        if (!$clientEmail) {
            return back()->with('error', 'O cliente deste Lead não possui e-mail cadastrado.');
        }

        try {
            // Gera o PDF em memória
            $pdf = Pdf::loadView('proposals.pdf', compact('proposal'));

            // Envia o email
            Mail::to($clientEmail)->send(new ProposalMail($proposal, $pdf->output()));

            // RF21: Atualiza status e data de envio
            $proposal->update([
                'status' => 'Enviada',
                'sent_at' => now(),
            ]);

            return back()->with('success', 'Proposta enviada por e-mail com sucesso!');

        } catch (\Exception $e) {
            Log::error('Erro no envio de proposta: ' . $e->getMessage());
            return back()->with('error', 'Falha ao enviar e-mail. Consulte o log.');
        }
    }

    /**
     * RF22: Aprovar Proposta -> Move Lead para Assinatura de Contrato.
     */
    public function approve(string $leadId, string $proposalId)
    {
        $proposal = Proposal::where('lead_id', $leadId)->findOrFail($proposalId);
        
        // 1. Atualiza status da proposta
        $proposal->update(['status' => 'Aceita']);

        // 2. Tenta mover a Lead para o estágio de Contrato
        $contractStage = PipelineStage::where('name', 'LIKE', '%Contrato%')
            ->orWhere('name', 'LIKE', '%Assinatura%')
            ->first();
        
        if ($contractStage) {
            $proposal->lead->update(['pipeline_stage_id' => $contractStage->id]);
        }

        // Redireciona para criação de contrato (fluxo sugerido pelo seu guia)
        return redirect()->route('leads.contract.create', $leadId)
            ->with('success', 'Proposta Aprovada! Lead movido para fase de Contrato.');
    }

    /**
     * RF22: Rejeitar Proposta -> Move Lead para Perdidos.
     */
    public function reject(string $leadId, string $proposalId)
    {
        $proposal = Proposal::where('lead_id', $leadId)->findOrFail($proposalId);

        // 1. Atualiza status da proposta
        $proposal->update(['status' => 'Rejeitada']);

        // 2. Tenta mover a Lead para estágio Perdido
        $lostStage = PipelineStage::where('name', 'LIKE', '%Perdido%')
             ->orWhere('name', 'LIKE', '%Lost%')
             ->first();

        if ($lostStage) {
            $proposal->lead->update([
                'pipeline_stage_id' => $lostStage->id,
                'status' => 'lost', // Campo de controle geral
                'is_won' => false,
                'closed_at' => now()
            ]);
        }

        return redirect()->route('leads.index')
            ->with('info', 'Proposta rejeitada e Lead marcado como Perdido.');
    }
}