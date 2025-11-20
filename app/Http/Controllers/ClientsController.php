<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ContactSource;
use App\Models\Lead;
use App\Models\SocialNetwork;
use Exception;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class ClientsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Se tiver filtro
        if ($request->filled('search')) {
            $clients = Client::withCount('leads')
                ->where(function ($query) use ($request) {
                    $query->where('name', 'LIKE', "%{$request->search}%")
                        ->orWhere('email', 'LIKE', "%{$request->search}%")
                        ->orWhere('cpf', 'LIKE', "%{$request->search}%");
                })
                ->orderBy('name', 'asc')
                ->paginate(10);
        } else {
            $clients = Client::withCount('leads')
                ->orderBy('name', 'asc')
                ->paginate(10);
        }

        return view('clients.index', compact('clients'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $contact_sources = ContactSource::all();
        $social_networks = SocialNetwork::all();

        return view('clients.create', compact('contact_sources', 'social_networks'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cpf' => [
                'required',
                Rule::unique('clients', 'cpf')->ignore($request->cpf)
            ],
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'address' => 'required',
            'city' => 'required',
            'state' => 'required|max:2',
            'contact_source_id' => 'required',
        ]);

        // Sanitização dos dados
        $validated['cpf'] = str_replace(['.','-'], "", $validated['cpf']);
        $validated['phone'] = str_replace([' ', '(', ')', '-'], "", $validated['phone']);
        $validated['owner_user_id'] = Auth::id();

        // Verifica se o client foi excluído (soft deleted)
        $client = Client::withTrashed()->where('cpf', $validated['cpf'])->first();

        // Se o cliente está soft-deleted => restaurar e fazer o update dos dados
        if ($client && $client->trashed()) {
            $client->restore();
            $client->update($validated);

        // Se for um novo cliente insere na tabela client
        } else {
            try {
                $client = Client::query()->create($validated);
            } catch (UniqueConstraintViolationException $exception) {
                Log::error($exception->getMessage());
                return back()->withErrors([
                    'error' => 'CPF já cadastrado',
                ]);
            } catch (Exception $exception) {
                Log::error($exception->getMessage());
                return back()->withErrors([
                    'error' => 'Ocorreu um erro ao tentar cadastrar o cliente',
                ]);
            }
        }

        // Insere as redes sociais na tabela client_social_network
        $social_networks = $request->input('social_networks', []);
        $this->insertSocialNetworks($client, $social_networks);

        // Se clicou em "Cadastrar Cliente e Ativar Lead"
        if ($request->action === "create_and_activate_lead") {
            return redirect()
                ->route('leads.create', ['client_id' => $client->id])
                ->with('success', 'Cliente cadastrado com sucesso');
        }

        return redirect()->route('clients.index')
            ->with('success', 'Cliente cadastrado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $client = Client::with(['socialNetworks', 'contactSource'])->findOrFail($id);
        $contact_sources = ContactSource::all();
        $social_networks = SocialNetwork::all();
        $leads = $client->leads()
            ->with(['owner', 'pipelineStage'])
            ->paginate(10);

        return view('clients.show', compact('client', 'contact_sources', 'social_networks', 'leads'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $social_networks = SocialNetwork::all();
        $contact_sources = ContactSource::all();
        $client = Client::with('socialNetworks')->findOrFail($id);

        return view('clients.edit', compact('client', 'social_networks', 'contact_sources'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'cpf' => 'required',
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'address' => 'required',
            'city' => 'required',
            'state' => 'required|max:2',
            'contact_source_id' => 'required',
        ]);

        // Sanitização dos dados
        $validated['cpf'] = str_replace(['.','-'], "", $validated['cpf']);
        $validated['phone'] = str_replace([' ', '(', ')', '-'], "", $validated['phone']);

        $client = Client::with('socialNetworks')->findOrFail($id);

        // Atualiza os dados do cliente
        try {
            $client->update($validated);
        } catch (UniqueConstraintViolationException $exception) {
            Log::error($exception->getMessage());
            return back()->withErrors([
                'error' => 'CPF já cadastrado',
            ]);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            return back()->withErrors([
                'error' => 'Ocorreu um erro ao tentar atualizar cadastro do cliente',
            ]);
        }

        // Insere as redes sociais na tabela client_social_network
        $social_networks = $request->input('social_networks', []);
        $this->insertSocialNetworks($client, $social_networks);

        return redirect()->route('clients.index')
            ->with('success', 'Cliente atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $client = Client::with('socialNetworks','leads','tasks','interactions','attachments')->findOrFail($id);
        $leads = Lead::with('diagnostic', 'proposal','contract','tasks','interactions','attachments')
                    ->where('client_id', $id)->get();

        try {
            // Apaga o cliente de todas as tabelas relacionadas
            $client->socialNetworks()->detach();
            $client->tasks()->delete();
            $client->interactions()->delete();
            $client->attachments()->delete();

            // Apaga tudo relacionado as leads
            if($leads->isNotEmpty()) {
                foreach ($leads as $lead) {
                    $lead->diagnostic()->delete();
                    $lead->proposal()->delete();
                    $lead->contract()->delete();
                }

                $client->leads()->delete();
            }

            $client->delete();
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            return back()->withErrors([
                'error' => 'Ocorreu um erro ao tentar remover o cliente',
            ]);
        }

        return redirect()->route('clients.index')
            ->with('success', 'Cliente excluído com sucesso!');
    }

    private function insertSocialNetworks(Client $client, $social_networks)
    {
        $social_networks_data = array();

        try {
            if(!empty($social_networks)) {
                foreach ($social_networks as $social_network) {
                    $social_networks_data[$social_network['id']] = ['profile_url' => $social_network['profile_url']];
                }

                $client->socialNetworks()->sync($social_networks_data);
            }
        } catch (UniqueConstraintViolationException $exception) {
            Log::error($exception->getMessage());
            return back()->withErrors([
                'error' => 'Erro: redes sociais duplicadas',
            ]);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            return back()->withErrors([
                'error' => 'Ocorreu um erro ao tentar cadastrar as redes sociais do cliente',
            ]);
        }
    }
}
