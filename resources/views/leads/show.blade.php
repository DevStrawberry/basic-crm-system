@extends('layouts.app')

@section('title', 'CRM - Visualizar Lead')

@section('content')
    <div class="w-full max-w-4xl mx-auto space-y-10">

        {{-- CARD PRINCIPAL --}}
        <div class="bg-white rounded-3xl shadow-2xl p-10 border border-gray-200">

            {{-- Título --}}
            <h2 class="text-4xl font-extrabold text-center text-gray-900 mb-10 tracking-tight">
                {{ $lead->title }}
            </h2>

            <div class="space-y-8">
                {{-- Descrição --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Descrição</label>
                    <p class="w-full border-2 border-gray-300 rounded-xl px-5 py-4 shadow-sm bg-gray-50 leading-relaxed">
                        {{ $lead->description }}
                    </p>
                </div>

                {{-- Cliente / Responsável --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Cliente</label>
                        <p class="w-full border-2 border-gray-300 rounded-xl px-5 py-3 shadow-sm bg-gray-50">{{ $lead->client->name }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Responsável</label>
                        <p class="w-full border-2 border-gray-300 rounded-xl px-5 py-3 shadow-sm bg-gray-50">{{ $lead->owner->name }}</p>
                    </div>
                </div>

                {{-- Status / Estágio --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Status</label>
                        <p class="w-full border-2 border-gray-300 rounded-xl px-5 py-3 shadow-sm bg-gray-50">
                            @switch($lead->status)
                                @case('new') Nova @break
                                @case('on_going') Em andamento @break
                                @case('completed') Finalizada @break
                                @case('lost') Perdida @break
                            @endswitch
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Estágio do Pipeline</label>
                        <p class="w-full border-2 border-gray-300 rounded-xl px-5 py-3 shadow-sm bg-gray-50">{{ $lead->pipelineStage->name }}</p>
                    </div>
                </div>

                {{-- Interesse / Data --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nível de Interesse</label>
                        <p class="w-full border-2 border-gray-300 rounded-xl px-5 py-3 shadow-sm bg-gray-50">{{ $lead->interest_levels }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Data de Cadastro</label>
                        <p class="w-full border-2 border-gray-300 rounded-xl px-5 py-3 shadow-sm bg-gray-50">{{ $lead->created_at->format('d/m/Y') }}</p>
                    </div>
                </div>

                {{-- Valor Estimado --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Valor Estimado</label>
                    <p class="w-full border-2 border-gray-300 rounded-xl px-5 py-3 shadow-sm bg-gray-50">R$ {{ number_format($lead->estimated_value, 2, ',', '.') }}</p>
                </div>

                {{-- Motivo da perda --}}
                @if($lead->pipelineStage->name === 'Perdida')
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Motivo da Perda</label>
                        <p class="w-full border-2 border-gray-300 rounded-xl px-5 py-3 shadow-sm bg-gray-50">{{ $lead->lostReason->description }}</p>
                    </div>
                @endif

                {{-- Encerramento --}}
                @if(!is_null($lead->closed_at))
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Data de Encerramento</label>
                        <p class="w-full border-2 border-gray-300 rounded-xl px-5 py-3 shadow-sm bg-gray-50">{{ $lead->closed_at }}</p>
                    </div>
                @endif
            </div>

            {{-- BOTÕES --}}
            <div class="mt-10 flex flex-wrap justify-between gap-4">

                <a href="{{ route('leads.index') }}"
                   class="inline-block bg-gray-300 hover:bg-gray-400 text-gray-900 font-bold py-3 px-6 rounded-xl shadow-lg transition cursor-pointer">
                    Voltar
                </a>

                <a href="{{ route('leads.edit', $lead->id) }}"
                   class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg transition cursor-pointer">
                    Editar
                </a>

                <a href="{{ route('leads.losts.create', $lead->id) }}"
                   class="inline-block bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg transition cursor-pointer">
                    Marcar como Perdida
                </a>

                {{-- Botões de mover pipeline --}}
                @switch($lead->pipeline_stage_id)
                    @case(1)
                        <a href="{{ route('leads.diagnostics.create', $lead->id) }}"
                           class="inline-block bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg transition cursor-pointer">
                            Avançar Pipeline → Diagnóstico
                        </a>
                        @break

                    @case(2)
                        <a href="{{ route('leads.proposals.create', $lead->id) }}"
                           class="inline-block bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg transition cursor-pointer">
                            Avançar Pipeline → Proposta
                        </a>
                        @break

                    @case(3)
                        <a href="{{ route('leads.contracts.create', ['lead_id' => $lead->id]) }}"
                           class="inline-block bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg transition cursor-pointer">
                            Avançar Pipeline → Contrato
                        </a>
                        @break

                    @case(4)
                        <a href="{{ route('leads.actives.create', ['lead_id' => $lead->id]) }}"
                           class="inline-block bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg transition cursor-pointer">
                            Tornar Cliente Ativo
                        </a>

                        <a href="{{ route('leads.losts.create', ['lead_id' => $lead->id]) }}"
                           class="inline-block bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg transition cursor-pointer">
                            Marcar como Perdida
                        </a>
                        @break
                @endswitch
            </div>
        </div>

        {{-- PIPELINE --}}
        <div class="bg-white rounded-3xl shadow-2xl p-10 border border-gray-200">
            <h3 class="text-3xl font-bold mb-6">Pipeline</h3>

            <div class="flex flex-wrap gap-4">

                @if($lead->diagnostic)
                    <a href="{{ route('leads.diagnostics.show', ['lead_id' => $lead->id, 'diagnostic' => $lead->diagnostic->id]) }}"
                       class="inline-block bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg transition cursor-pointer">
                        Ver Diagnóstico
                    </a>
                @endif

                @if($lead->proposal)
                    {{-- CORREÇÃO: 'diagnostic_id' -> 'proposal', e usando ID da proposta --}}
                    <a href="{{ route('leads.proposals.show', ['lead_id' => $lead->id, 'proposal' => $lead->proposal->id]) }}"
                       class="inline-block bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg transition cursor-pointer">
                        Ver Proposta
                    </a>
                @endif

                @if($lead->contract)
                    <a href="{{ route('leads.contract.show', ['lead_id' => $lead->id]) }}"
                       class="inline-block bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg transition cursor-pointer">
                        Ver Contrato
                    </a>
                @endif
            </div>
        </div>
        <div class="bg-white rounded-3xl shadow-2xl p-10 border border-gray-200">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-3xl font-bold text-gray-800">Propostas da Lead</h3>
                <a href="{{ route('leads.proposals.create', $lead) }}"
                   class="inline-block bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg transition cursor-pointer">
                    + Nova Proposta
                </a>
            </div>

            <div class="bg-white rounded-3xl shadow-2xl p-10 border border-gray-200">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-3xl font-bold text-gray-800">Proposta da Lead</h3>
                {{-- Se a proposta NÃO existir, mostra o botão de criar --}}
                @if(!$lead->proposal)
                <a href="{{ route('leads.proposals.create', $lead) }}"
                   class="inline-block bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg transition cursor-pointer">
                    + Nova Proposta
                </a>
                @endif
            </div>

            {{-- Se a proposta JÁ EXISTE, mostra a tabela com uma linha --}}
            @if($lead->proposal)
                <div class="overflow-x-auto">
                    <table class="w-full text-left border border-gray-200 rounded-xl shadow-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-3 border-b font-semibold text-gray-700">Título</th>
                                <th class="px-4 py-3 border-b font-semibold text-gray-700">Assessor</th>
                                <th class="px-4 py-3 border-b font-semibold text-gray-700">Valor</th>
                                <th class="px-4 py-3 border-b font-semibold text-gray-700">Status</th>
                                <th class="px-4 py-3 border-b font-semibold text-gray-700">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Sem @foreach, pois é apenas um item --}}
                            @php
                                $proposal = $lead->proposal; // Apenas para facilitar a leitura
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 border-b">{{ $proposal->title }}</td>
                                <td class="px-4 py-3 border-b">{{ $proposal->assessor->name ?? 'N/A' }}</td>
                                <td class="px-4 py-3 border-b">R$ {{ number_format($proposal->total_value, 2, ',', '.') }}</td>
                                <td class="px-4 py-3 border-b">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($proposal->status == 'Enviada') bg-blue-100 text-blue-800
                                        @elseif($proposal->status == 'Aceita') bg-green-100 text-green-800
                                        @elseif($proposal->status == 'Rejeitada') bg-red-100 text-red-800
                                        @else bg-gray-200 text-gray-800 @endif">
                                        {{ $proposal->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 border-b space-x-3">
                                    <a href="{{ route('leads.proposals.show', ['lead_id' => $lead->id, 'proposal' => $proposal->id]) }}" class="text-indigo-600 hover:underline">Ver</a>
                                    @if($proposal->status === 'Draft')
                                        <a href="{{ route('leads.proposals.edit', ['lead_id' => $lead->id, 'proposal' => $proposal->id]) }}" class="text-green-600 hover:underline">Editar</a>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            @else
                {{-- Se a proposta NÃO existir, mostra a mensagem --}}
                <p class="text-gray-500 text-center">Nenhuma proposta cadastrada para esta lead.</p>
            @endif
        </div>

    </div>
@endsection
