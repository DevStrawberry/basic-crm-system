<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Lead;
use App\Models\PipelineStage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeadsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $pipelineStages = PipelineStage::all();
        $leads = Lead::query()->with('client', 'owner', 'pipelineStage');

        // Filtro por nome do cliente
        if ($request->filled('search')) {
            $leads->whereHas('client', function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->input('search') . '%');
                });
        }

        // Filtro por status da lead
        if ($request->filled('status')) {
            $leads->where('status', '=', $request->input('status'));
        }

        // Filtro por estágio do pipeline
        if ($request->filled('pipeline_stage')) {
            $leads->where('pipeline_stage_id', '=', $request->input('pipeline_stage'));
        }

        $leads = $leads->paginate(10);

        return view('leads.index', compact('leads', 'pipelineStages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $clients = Client::query()->select('id', 'name')->get();
        $users = User::query()->select('id', 'name')
            ->whereNot('role_id', 1)
            ->get();

        return view('leads.create', compact('clients', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required',
            'title' => 'required',
            'description' => 'required',
            'estimated_value' => 'required|numeric',
            'interest_levels' => 'required',
            'status' => 'required',
            'pipeline_stage_id' => 'required',
        ]);

        // Se o gestor atribuiu um responsável
        if ($request->owner_id) {
            $validated['owner_id'] = $request->input('owner_id');
        } else {
            $validated['owner_id'] = Auth::user()->getAuthIdentifier();
        }

        if (Lead::create($validated)) {
            return redirect()->route('leads.index')
                ->with('success', 'Lead criada com sucesso');
        }

        return redirect()->route('leads.create')
            ->with('error', 'Erro ao cadastrar Lead');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $lead = Lead::with('client', 'owner', 'pipelineStage',
            'lostReason', 'diagnostic', 'proposal', 'contract')->findOrFail($id);

        return view('leads.show', compact('lead'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $lead = Lead::with('client', 'owner', 'pipelineStage',
            'lostReason', 'diagnostic', 'proposal', 'contract')->findOrFail($id);
        $clients = Client::query()->select('id', 'name')->get();
        $users = User::query()->select('id', 'name')
            ->whereNot('role_id', '=', 1)->get();

        return view('leads.edit', compact('lead', 'clients', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'client_id' => 'required',
            'title' => 'required',
            'description' => 'required',
            'estimated_value' => 'required|numeric',
            'interest_levels' => 'required',
            'status' => 'required',
            'pipeline_stage_id' => 'required',
        ]);

        $lead = Lead::query()->findOrFail($id);

        if($lead->update($validated)) {
            return redirect()->route('leads.index')
                ->with('success', 'Lead atualizada com sucesso');
        };

        return redirect()->route('leads.index')
            ->withErrors(['error' => 'Erro ao atualizar lead']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $lead = Lead::query()->findOrFail($id);
        $lead->delete();

        return redirect()->route('leads.index')->with('success', 'Lead excluída com sucesso');
    }
}
