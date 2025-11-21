@extends('layouts.app')

@section('title', 'Propostas do Lead #' . $leadId)

@section('content')
    <div class="container mx-auto p-4">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Propostas Comerciais</h1>
                <p class="text-gray-600">Lead: {{ $lead->name ?? '#' . $leadId }}</p>
            </div>
            <a href="{{ route('leads.proposals.create', $leadId) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-300">
                + Nova Proposta
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if($proposals->isEmpty())
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 text-yellow-700">
                <p>Nenhuma proposta registrada para este cliente.</p>
            </div>
        @else
            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg bg-white">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Título</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Validade</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($proposals as $proposal)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $proposal->title }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                R$ {{ number_format($proposal->total_value, 2, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($proposal->status == 'Aceita') bg-green-100 text-green-800 
                                    @elseif($proposal->status == 'Rejeitada') bg-red-100 text-red-800 
                                    @elseif($proposal->status == 'Enviada') bg-blue-100 text-blue-800 
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ $proposal->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($proposal->valid_until)->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('leads.proposals.show', [$leadId, $proposal->id]) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Ver</a>
                                @if($proposal->status === 'Draft')
                                    <a href="{{ route('leads.proposals.edit', [$leadId, $proposal->id]) }}" class="text-yellow-600 hover:text-yellow-900">Editar</a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $proposals->links() }}
            </div>
        @endif
        
        <div class="mt-6">
            <a href="{{ route('leads.show', $leadId) }}" class="text-indigo-600 hover:text-indigo-800 text-sm">← Voltar para Lead</a>
        </div>
    </div>
@endsection