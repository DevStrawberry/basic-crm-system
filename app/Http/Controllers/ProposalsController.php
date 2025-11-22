<?php

namespace App\Http\Controllers;

use App\Mail\ProposalMail;
use App\Models\Lead;
use App\Models\PipelineStage;
use App\Models\Proposal;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ProposalsController extends Controller
{
    public function index(string $leadId)
    {
        $lead = Lead::findOrFail($leadId);
        $proposals = Proposal::with('createdBy')
            ->where('lead_id', $leadId)
            ->latest()
            ->paginate(10);

        return view('proposals.index', compact('lead', 'proposals', 'leadId'));
    }

    public function create(string $leadId)
    {
        return view('proposals.create', compact('leadId'));
    }

    public function store(Request $request, string $leadId)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'service_description' => 'required|string',
            'total_value' => 'required|numeric|min:0',
            'valid_until' => 'required|date|after_or_equal:today',
            'warranties' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        try {
            Proposal::create([
                'lead_id' => $leadId,
                'created_by' => Auth::id(),
                'status' => 'Draft',
                ...$validated
            ]);

            return redirect()->route('leads.proposals.index', ['lead_id' => $leadId])
                ->with('success', 'Proposta gerada com sucesso!');

        } catch (\Exception $e) {
            Log::error('Erro ao criar proposta: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Erro ao salvar proposta.'])->withInput();
        }
    }

    public function show(string $leadId, string $proposalId)
    {
        $proposal = Proposal::with(['lead.client', 'createdBy'])
            ->where('lead_id', $leadId)
            ->findOrFail($proposalId);

        return view('proposals.show', compact('proposal', 'leadId'));
    }

    public function edit(string $leadId, string $proposalId)
    {
        $proposal = Proposal::where('lead_id', $leadId)->findOrFail($proposalId);

        if ($proposal->status !== 'Draft') {
            return back()->withErrors(['error' => 'Apenas propostas em rascunho (Draft) podem ser editadas.']);
        }

        return view('proposals.edit', compact('proposal', 'leadId'));
    }

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

        return redirect()->route('leads.proposals.show', ['lead_id' => $leadId, 'proposal' => $proposalId])
            ->with('success', 'Proposta atualizada com sucesso!');
    }

    public function destroy(string $leadId, string $proposalId)
    {
        $proposal = Proposal::where('lead_id', $leadId)->findOrFail($proposalId);
        $proposal->delete();

        return redirect()->route('leads.proposals.index', ['lead_id' => $leadId])
            ->with('success', 'Proposta excluída.');
    }

    public function generatePdf(string $leadId, string $proposalId)
    {
        $proposal = Proposal::with(['lead.client', 'createdBy'])->where('lead_id', $leadId)->findOrFail($proposalId);
        
        $pdf = Pdf::loadView('proposals.pdf', compact('proposal'));
        
        return $pdf->stream('proposta_' . $proposal->id . '.pdf');
    }

    /**
     * Envio de Email SIMPLIFICADO (Sem PDF por enquanto)
     */
    public function sendEmail(string $leadId, string $proposalId)
    {
        $proposal = Proposal::with(['lead.client', 'createdBy'])->where('lead_id', $leadId)->findOrFail($proposalId);
        $clientEmail = $proposal->lead->client->email;

        if (!$clientEmail) {
            return back()->withErrors(['error' => 'O cliente deste Lead não possui e-mail cadastrado.']);
        }

        try {
            // TEMP: Comentei a geração do PDF para teste
            // $pdf = Pdf::loadView('proposals.pdf', compact('proposal'));
            // $pdfContent = $pdf->output();

            // Passamos null no lugar do conteúdo do PDF
            Mail::to($clientEmail)->send(new ProposalMail($proposal, null));

            $proposal->update([
                'status' => 'Enviada',
                'sent_at' => now(),
            ]);

            return back()->with('success', 'Proposta enviada por e-mail com sucesso! (Sem anexo)');

        } catch (\Exception $e) {
            Log::error('Erro no envio de proposta: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Falha no envio do e-mail. Verifique o log.']);
        }
    }

    public function approve(string $leadId, string $proposalId)
    {
        $proposal = Proposal::where('lead_id', $leadId)->findOrFail($proposalId);
        
        $proposal->update(['status' => 'Aceita']);

        $contractStage = PipelineStage::where('name', 'LIKE', '%Contrato%')
            ->orWhere('name', 'LIKE', '%Assinatura%')
            ->first();
        
        if ($contractStage) {
            $proposal->lead->update(['pipeline_stage_id' => $contractStage->id]);
        }

        return redirect()->route('leads.contract.create', ['lead_id' => $leadId])
            ->with('success', 'Proposta Aprovada! Lead movido para fase de Contrato.');
    }

    public function reject(string $leadId, string $proposalId)
    {
        $proposal = Proposal::where('lead_id', $leadId)->findOrFail($proposalId);

        $proposal->update(['status' => 'Rejeitada']);

        $lostStage = PipelineStage::where('name', 'LIKE', '%Perdido%')
             ->orWhere('name', 'LIKE', '%Lost%')
             ->first();

        if ($lostStage) {
            $proposal->lead->update([
                'pipeline_stage_id' => $lostStage->id,
                'status' => 'lost',
                'is_won' => false,
                'closed_at' => now()
            ]);
        }

        return redirect()->route('leads.index')
            ->with('info', 'Proposta rejeitada e Lead marcado como Perdido.');
    }
}