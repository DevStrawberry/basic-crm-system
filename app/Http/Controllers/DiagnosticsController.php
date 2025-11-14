<?php

namespace App\Http\Controllers;

use App\Models\Diagnostic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class DiagnosticsController extends Controller
{
    /**
     * Display a listing of the resource.
     * Deve listar apenas diagnósticos para o Lead específico ($leadId).
     */
    public function index(string $leadId)
    {
        $user = Auth::user();
        $diagnostics = Diagnostic::query()
            ->where('lead_id', $leadId)
            ->with(['lead', 'diagnosedBy']);

        if (method_exists($user, 'isManager') && !$user->isManager()) {
            $diagnostics->where('diagnosed_by_id', $user->id);
        }

        $diagnostics = $diagnostics->latest()->get();

        return view('diagnostics.index', compact('diagnostics', 'leadId'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(string $leadId)
    {
        return view('diagnostics.create', compact('leadId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $leadId)
    {
        $validatedData = $request->validate([
            'problem_description' => 'required|string|max:1000',
            'customer_needs' => 'required|string|max:1000',
            'possible_solutions' => 'required|string|max:1000',
            'urgency_level' => 'required|in:Baixa,Média,Alta',
        ]);

        $diagnostic = Diagnostic::create([
            ...$validatedData,
            'lead_id' => $leadId,
            'diagnosed_by_id' => Auth::id(),
        ]);

        return redirect()->route('leads.diagnostics.show', ['lead_id' => $leadId, 'diagnostic' => $diagnostic->id])
            ->with('success', 'Diagnóstico registrado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $leadId, string $diagnosticId)
    {
        $diagnostic = Diagnostic::with(['lead', 'diagnosedBy', 'attachments'])
            ->where('lead_id', $leadId)
            ->findOrFail($diagnosticId);

        if (method_exists(Auth::user(), 'isManager') && !Auth::user()->isManager()) {
            if ($diagnostic->diagnosed_by_id !== Auth::id()) {
                abort(403);
            }
        }

        return view('diagnostics.show', compact('diagnostic', 'leadId'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $leadId, string $diagnosticId)
    {
        $diagnostic = Diagnostic::where('lead_id', $leadId)->findOrFail($diagnosticId);

        if ($diagnostic->diagnosed_by_id !== Auth::id()) {
            abort(403);
        }

        return view('diagnostics.edit', compact('diagnostic', 'leadId'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $leadId, string $diagnosticId)
    {
        $diagnostic = Diagnostic::where('lead_id', $leadId)->findOrFail($diagnosticId);

        if ($diagnostic->diagnosed_by_id !== Auth::id()) {
            abort(403);
        }

        $validatedData = $request->validate([
            'problem_description' => 'required|string|max:1000',
            'customer_needs' => 'required|string|max:1000',
            'possible_solutions' => 'required|string|max:1000',
            'urgency_level' => 'required|in:Baixa,Média,Alta',
        ]);

        $diagnostic->update($validatedData);

        return redirect()->route('leads.diagnostics.show', ['lead_id' => $leadId, 'diagnostic' => $diagnostic->id])
            ->with('success', 'Diagnóstico atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $leadId, string $diagnosticId)
    {
        $diagnostic = Diagnostic::where('lead_id', $leadId)->findOrFail($diagnosticId);

        if ($diagnostic->diagnosed_by_id !== Auth::id() && (method_exists(Auth::user(), 'isManager') && !Auth::user()->isManager())) {
            abort(403);
        }

        $diagnostic->delete();

        return redirect()->route('leads.diagnostics.index', $leadId)
            ->with('success', 'Diagnóstico excluído com sucesso!');
    }
}
