<?php

namespace App\Http\Controllers;

use App\Mail\ContractSignedMail;
use App\Models\Attachment;
use App\Models\Contract;
use App\Models\Interaction;
use App\Models\Lead;
use App\Models\PipelineStage;
use App\Models\Proposal;
use App\Models\User;
use App\Services\ContractPdfService;
use App\Services\SignatureService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class ContractsController extends Controller
{
    private SignatureService $signatureService;
    private ContractPdfService $pdfService;

    public function __construct(SignatureService $signatureService, ContractPdfService $pdfService)
    {
        $this->signatureService = $signatureService;
        $this->pdfService = $pdfService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(string $leadId)
    {
        $user = Auth::user();
        $roleName = strtolower($user->role?->name ?? '');

        $contracts = Contract::query()
            ->where('lead_id', $leadId)
            ->with(['lead.client', 'proposal', 'assignedTo']);

        if ($roleName === 'assessor') {
            $contracts->where('assigned_to', $user->id);
        }

        $contracts = $contracts->latest()->get();

        return view('contracts.index', compact('contracts', 'leadId'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(string $leadId)
    {
        $lead = Lead::with('client')->findOrFail($leadId);
        $proposals = Proposal::where('lead_id', $leadId)->where('status', 'Aceita')->get();
        $assessors = User::whereHas('role', function ($query) {
            $query->where('name', 'Assessor');
        })->get();

        return view('contracts.create', compact('lead', 'proposals', 'assessors', 'leadId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $leadId)
    {
        $validated = $request->validate([
            'proposal_id' => 'required|exists:proposals,id',
            'contract_number' => 'required|integer|unique:contracts,contract_number',
            'final_value' => 'required|numeric|min:0',
            'payment_method' => 'required|string|max:255',
            'deadline' => 'nullable|date',
            'assigned_to' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
            'contract_file' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ]);

        try {
            DB::beginTransaction();

            $contract = Contract::create([
                'lead_id' => $leadId,
                'proposal_id' => $validated['proposal_id'],
                'contract_number' => $validated['contract_number'],
                'final_value' => $validated['final_value'],
                'payment_method' => $validated['payment_method'],
                'deadline' => $validated['deadline'] ?? null,
                'assigned_to' => $validated['assigned_to'] ?? Auth::id(),
                'status' => 'Em elaboração',
                'notes' => $validated['notes'] ?? null,
            ]);

            // Upload de arquivo se fornecido
            if ($request->hasFile('contract_file')) {
                $file = $request->file('contract_file');
                $filename = 'contract_' . $contract->id . '_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('contracts', $filename, 'public');
                
                $contract->update(['file_path' => $path]);

                // Criar attachment
                Attachment::create([
                    'related_table' => 'contracts',
                    'related_id' => $contract->id,
                    'filename' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'content_type' => $file->getMimeType(),
                    'uploaded_by' => Auth::id(),
                ]);
            }

            DB::commit();

            return redirect()->route('leads.contract.show', ['lead_id' => $leadId, 'contract' => $contract->id])
                ->with('success', 'Contrato cadastrado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao cadastrar contrato', [
                'error' => $e->getMessage(),
                'lead_id' => $leadId
            ]);

            return back()->withErrors(['error' => 'Erro ao cadastrar contrato. Tente novamente.'])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $leadId, string $contractId)
    {
        $contract = Contract::with([
            'lead.client',
            'proposal',
            'assignedTo',
            'interactions.createdBy',
            'attachments'
        ])->where('lead_id', $leadId)->findOrFail($contractId);

        $user = Auth::user();
        $roleName = strtolower($user->role?->name ?? '');

        // Verificar permissão
        if ($roleName === 'assessor' && $contract->assigned_to !== $user->id) {
            abort(403, 'Você não tem permissão para visualizar este contrato.');
        }

        return view('contracts.show', compact('contract', 'leadId'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $leadId, string $contractId)
    {
        $contract = Contract::where('lead_id', $leadId)->findOrFail($contractId);
        $proposals = Proposal::where('lead_id', $leadId)->get();
        $assessors = User::whereHas('role', function ($query) {
            $query->where('name', 'Assessor');
        })->get();

        return view('contracts.edit', compact('contract', 'proposals', 'assessors', 'leadId'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $leadId, string $contractId)
    {
        $contract = Contract::where('lead_id', $leadId)->findOrFail($contractId);

        $validated = $request->validate([
            'proposal_id' => 'required|exists:proposals,id',
            'contract_number' => 'required|integer|unique:contracts,contract_number,' . $contractId,
            'final_value' => 'required|numeric|min:0',
            'payment_method' => 'required|string|max:255',
            'deadline' => 'nullable|date',
            'assigned_to' => 'nullable|exists:users,id',
            'status' => 'required|in:Em elaboração,Aguardando Assinatura,Assinado,Fechado,Cancelado',
            'notes' => 'nullable|string',
            'contract_file' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ]);

        try {
            $contract->update($validated);

            // Upload de arquivo se fornecido
            if ($request->hasFile('contract_file')) {
                // Remove arquivo antigo se existir
                if ($contract->file_path && Storage::disk('public')->exists($contract->file_path)) {
                    Storage::disk('public')->delete($contract->file_path);
                }

                $file = $request->file('contract_file');
                $filename = 'contract_' . $contract->id . '_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('contracts', $filename, 'public');
                
                $contract->update(['file_path' => $path]);

                // Criar attachment
                Attachment::create([
                    'related_table' => 'contracts',
                    'related_id' => $contract->id,
                    'filename' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'content_type' => $file->getMimeType(),
                    'uploaded_by' => Auth::id(),
                ]);
            }

            return redirect()->route('leads.contract.show', ['lead_id' => $leadId, 'contract' => $contract->id])
                ->with('success', 'Contrato atualizado com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar contrato', [
                'error' => $e->getMessage(),
                'contract_id' => $contractId
            ]);

            return back()->withErrors(['error' => 'Erro ao atualizar contrato. Tente novamente.'])->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $leadId, string $contractId)
    {
        $contract = Contract::where('lead_id', $leadId)->findOrFail($contractId);

        try {
            // Remove arquivo se existir
            if ($contract->file_path && Storage::disk('public')->exists($contract->file_path)) {
                Storage::disk('public')->delete($contract->file_path);
            }

            $contract->delete();

            return redirect()->route('leads.contract.index', $leadId)
                ->with('success', 'Contrato excluído com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao excluir contrato', [
                'error' => $e->getMessage(),
                'contract_id' => $contractId
            ]);

            return back()->withErrors(['error' => 'Erro ao excluir contrato. Tente novamente.']);
        }
    }

    /**
     * Listar os próprios contratos (Assessor)
     */
    public function myList()
    {
        $user = Auth::user();
        $contracts = Contract::with(['lead.client', 'proposal', 'assignedTo'])
            ->where('assigned_to', $user->id)
            ->latest()
            ->paginate(15);

        return view('contracts.my-list', compact('contracts'));
    }

    /**
     * Listar todos os contratos (Gestor) com filtros
     */
    public function all(Request $request)
    {
        $query = Contract::with(['lead.client', 'proposal', 'assignedTo']);

        // Filtros
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('contract_number', 'LIKE', "%{$search}%")
                    ->orWhereHas('lead.client', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%{$search}%")
                            ->orWhere('email', 'LIKE', "%{$search}%");
                    });
            });
        }

        $contracts = $query->latest()->paginate(15);
        $assessors = User::whereHas('role', function ($q) {
            $q->where('name', 'Assessor');
        })->get();

        return view('contracts.all', compact('contracts', 'assessors'));
    }

    /**
     * Registrar interação com o contrato
     */
    public function interact(Request $request, string $leadId, string $contractId)
    {
        $contract = Contract::where('lead_id', $leadId)->findOrFail($contractId);

        $validated = $request->validate([
            'type' => 'required|in:Ligação,E-mail,Reunião,Mensagem,Nota',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'happened_at' => 'nullable|date',
        ]);

        try {
            Interaction::create([
                'lead_id' => $leadId,
                'client_id' => $contract->lead->client_id,
                'contract_id' => $contractId,
                'created_by' => Auth::id(),
                'type' => $validated['type'],
                'subject' => $validated['subject'],
                'body' => $validated['body'],
                'happened_at' => $validated['happened_at'] ?? now(),
            ]);

            return redirect()->route('leads.contract.show', ['lead_id' => $leadId, 'contract' => $contractId])
                ->with('success', 'Interação registrada com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao registrar interação', [
                'error' => $e->getMessage(),
                'contract_id' => $contractId
            ]);

            return back()->withErrors(['error' => 'Erro ao registrar interação. Tente novamente.'])->withInput();
        }
    }

    /**
     * Atribuir contrato a um assessor (Gestor)
     * 
     * SECURITY: Apenas Gestores podem atribuir contratos
     */
    public function assign(Request $request, string $leadId, string $contractId)
    {
        $user = Auth::user();
        $roleName = strtolower($user->role?->name ?? '');

        // Validação de permissão: apenas Gestor pode atribuir contratos
        if ($roleName !== 'gestor') {
            abort(403, 'Apenas gestores podem atribuir contratos.');
        }

        $contract = Contract::with('assignedTo')->where('lead_id', $leadId)->findOrFail($contractId);

        $validated = $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);

        // Verificar se o usuário alvo é realmente um Assessor
        $newAssessor = User::with('role')->findOrFail($validated['assigned_to']);
        if (strtolower($newAssessor->role?->name ?? '') !== 'assessor') {
            return back()->withErrors(['assigned_to' => 'O usuário selecionado deve ser um Assessor.']);
        }

        try {
            $oldAssessorId = $contract->assigned_to;
            $oldAssessorName = $contract->assignedTo ? $contract->assignedTo->name : 'Não atribuído';
            
            $contract->update(['assigned_to' => $validated['assigned_to']]);

            // Registrar a alteração no log
            Log::info('Contrato atribuído', [
                'contract_id' => $contractId,
                'contract_number' => $contract->contract_number,
                'old_assessor_id' => $oldAssessorId,
                'old_assessor_name' => $oldAssessorName,
                'new_assessor_id' => $validated['assigned_to'],
                'new_assessor_name' => $newAssessor->name,
                'assigned_by' => $user->id,
                'assigned_by_name' => $user->name,
                'timestamp' => now()->toDateTimeString()
            ]);

            return redirect()->route('leads.contract.show', ['lead_id' => $leadId, 'contract' => $contractId])
                ->with('success', 'Contrato atribuído com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao atribuir contrato', [
                'error' => $e->getMessage(),
                'contract_id' => $contractId,
                'user_id' => $user->id
            ]);

            return back()->withErrors(['error' => 'Erro ao atribuir contrato. Tente novamente.']);
        }
    }

    /**
     * Assinar contrato - gera PDF, envia email e move lead
     */
    public function sign(Request $request, string $leadId, string $contractId)
    {
        $contract = Contract::with(['lead.client', 'proposal'])->where('lead_id', $leadId)->findOrFail($contractId);

        $validated = $request->validate([
            'signed_by' => 'required|string|max:255',
            'move_lead_status' => 'nullable|in:client,lost',
        ]);

        try {
            DB::beginTransaction();

            $lead = $contract->lead;
            $pdfPath = null;

            // Se for marcado como perdido, não assina, apenas cancela
            if ($validated['move_lead_status'] === 'lost') {
                // Buscar pipeline stage "Cliente Perdido"
                $clientePerdidoStage = PipelineStage::where('name', 'Cliente Perdido')->first();
                
                // Atualizar status do contrato para Cancelado (não assina)
                $contract->update(['status' => 'Cancelado']);
                
                if ($clientePerdidoStage) {
                    // Marcar lead como perdido e atualizar pipeline stage
                    $lead->update([
                        'is_won' => false,
                        'closed_at' => now(),
                        'pipeline_stage_id' => $clientePerdidoStage->id,
                    ]);
                    
                    Log::info('Lead movido para Cliente Perdido e contrato cancelado', [
                        'lead_id' => $lead->id,
                        'contract_id' => $contractId,
                        'pipeline_stage_id' => $clientePerdidoStage->id
                    ]);
                } else {
                    // Fallback: apenas atualizar is_won e closed_at se stage não existir
                    $lead->update([
                        'is_won' => false,
                        'closed_at' => now(),
                    ]);
                    
                    Log::warning('Pipeline stage "Cliente Perdido" não encontrado', [
                        'lead_id' => $lead->id,
                        'contract_id' => $contractId
                    ]);
                }

                DB::commit();

                return redirect()->route('leads.contract.show', ['lead_id' => $leadId, 'contract' => $contractId])
                    ->with('success', 'Contrato cancelado e Lead marcado como perdido.');
            }

            // Fluxo de assinatura (sucesso)
            // 1. Solicitar assinatura digital (simulação)
            $signatureResponse = $this->signatureService->requestSignature($contract);

            if (!$signatureResponse['success']) {
                throw new \Exception('Falha ao solicitar assinatura digital');
            }

            // 2. Gerar PDF
            $pdfPath = $this->pdfService->generatePdf($contract);

            // 3. Atualizar contrato para Assinado
            $contract->update([
                'status' => 'Assinado',
                'signed_by' => $validated['signed_by'],
                'signed_at' => now(),
                'file_path' => $pdfPath ?? $contract->file_path,
            ]);

            // 4. Enviar email com PDF anexado
            if ($contract->lead->client->email) {
                try {
                    Mail::to($contract->lead->client->email)->send(new ContractSignedMail($contract, $pdfPath));
                    
                    // Enviar também para o assessor se houver
                    if ($contract->assignedTo && $contract->assignedTo->email) {
                        Mail::to($contract->assignedTo->email)->send(new ContractSignedMail($contract, $pdfPath));
                    }
                } catch (\Exception $e) {
                    Log::warning('Erro ao enviar email do contrato assinado', [
                        'error' => $e->getMessage(),
                        'contract_id' => $contractId
                    ]);
                    // Não interrompe o processo se o email falhar
                }
            }

            // 5. Mover Lead para Cliente (se solicitado)
            if ($validated['move_lead_status'] === 'client') {
                // Buscar pipeline stage "Cliente Ativo"
                $clienteAtivoStage = PipelineStage::where('name', 'Cliente Ativo')->first();
                
                if ($clienteAtivoStage) {
                    // Marcar lead como vencido (cliente) e atualizar pipeline stage
                    $lead->update([
                        'is_won' => true,
                        'closed_at' => now(),
                        'pipeline_stage_id' => $clienteAtivoStage->id,
                    ]);
                    
                    Log::info('Lead movido para Cliente Ativo após assinatura de contrato', [
                        'lead_id' => $lead->id,
                        'contract_id' => $contractId,
                        'pipeline_stage_id' => $clienteAtivoStage->id
                    ]);
                } else {
                    // Fallback: apenas atualizar is_won e closed_at se stage não existir
                    $lead->update([
                        'is_won' => true,
                        'closed_at' => now(),
                    ]);
                    
                    Log::warning('Pipeline stage "Cliente Ativo" não encontrado', [
                        'lead_id' => $lead->id,
                        'contract_id' => $contractId
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('leads.contract.show', ['lead_id' => $leadId, 'contract' => $contractId])
                ->with('success', 'Contrato assinado com sucesso! PDF gerado e email enviado.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao assinar contrato', [
                'error' => $e->getMessage(),
                'contract_id' => $contractId
            ]);

            return back()->withErrors(['error' => 'Erro ao assinar contrato: ' . $e->getMessage()])->withInput();
        }
    }
}
