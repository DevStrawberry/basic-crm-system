@extends('layouts.app')

@section('title', 'Detalhes da Proposta #' . $proposal->id)

@section('content')
<div class="container mx-auto p-4">
    <div class="bg-white p-8 rounded-lg shadow-2xl max-w-5xl mx-auto">
        
        {{-- Cabeçalho --}}
        <div class="flex justify-between items-start mb-6 border-b pb-4">
            <div>
                <h1 class="text-3xl font-extrabold text-indigo-700">{{ $proposal->title }}</h1>
                <p class="text-gray-500">Status: 
                    <span class="font-bold 
                        @if($proposal->status == 'Aceita') text-green-600 
                        @elseif($proposal->status == 'Rejeitada') text-red-600 
                        @elseif($proposal->status == 'Enviada') text-blue-600
                        @else text-yellow-600 @endif">
                        {{ $proposal->status }}
                    </span>
                </p>
            </div>
            
            {{-- Botões de Ação Topo --}}
            @if($proposal->status === 'Draft')
            <div class="flex space-x-2">
                <a href="{{ route('leads.proposals.edit', [$leadId, $proposal->id]) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded shadow">
                    Editar
                </a>
                <form action="{{ route('leads.proposals.destroy', [$leadId, $proposal->id]) }}" method="POST" onsubmit="return confirm('Excluir proposta permanentemente?');">
                    @csrf @method('DELETE')
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded shadow">
                        Excluir
                    </button>
                </form>
            </div>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            
            {{-- Coluna da Esquerda: Detalhes --}}
            <div class="md:col-span-2 space-y-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-700">Descrição dos Serviços</h3>
                    <div class="bg-gray-50 p-4 rounded border text-gray-800 whitespace-pre-line">{{ $proposal->service_description }}</div>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-gray-700">Garantias</h3>
                    <div class="bg-gray-50 p-4 rounded border text-gray-800 whitespace-pre-line">{{ $proposal->warranties }}</div>
                </div>

                @if($proposal->notes)
                <div>
                    <h3 class="text-lg font-semibold text-gray-700">Observações Internas</h3>
                    <div class="text-sm text-gray-600 bg-yellow-50 p-3 rounded border border-yellow-100">{{ $proposal->notes }}</div>
                </div>
                @endif
            </div>

            {{-- Coluna da Direita: Resumo e Ações --}}
            <div class="space-y-6">
                <div class="bg-indigo-50 p-5 rounded-lg border border-indigo-100">
                    <h3 class="text-indigo-800 font-bold text-lg mb-2">Resumo Comercial</h3>
                    <p class="flex justify-between text-sm mb-1"><span>Valor Total:</span> <span class="font-bold text-lg">R$ {{ number_format($proposal->total_value, 2, ',', '.') }}</span></p>
                    <p class="flex justify-between text-sm mb-1"><span>Válida até:</span> <span>{{ $proposal->valid_until->format('d/m/Y') }}</span></p>
                    <p class="flex justify-between text-sm"><span>Criada por:</span> <span>{{ $proposal->createdBy->name ?? 'N/A' }}</span></p>
                    @if($proposal->sent_at)
                        <p class="flex justify-between text-sm mt-2 text-blue-600"><span>Enviada em:</span> <span>{{ $proposal->sent_at->format('d/m/Y') }}</span></p>
                    @endif
                </div>

                <div class="border-t pt-4">
                    <h4 class="text-sm font-bold text-gray-500 uppercase mb-3">Documentos & Envio</h4>
                    <a href="{{ route('leads.proposals.pdf', [$leadId, $proposal->id]) }}" target="_blank" class="block w-full text-center bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded mb-2">
                        <i class="fas fa-file-pdf"></i> Visualizar PDF
                    </a>
                    
                    <form action="{{ route('leads.proposals.email', [$leadId, $proposal->id]) }}" method="POST">
                        @csrf
                        <button type="submit" class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition" onclick="return confirm('Enviar PDF por e-mail para o cliente?')">
                            <i class="fas fa-envelope"></i> Enviar por E-mail
                        </button>
                    </form>
                </div>

                @if($proposal->status !== 'Aceita' && $proposal->status !== 'Rejeitada')
                <div class="border-t pt-4">
                    <h4 class="text-sm font-bold text-gray-500 uppercase mb-3">Decisão do Cliente</h4>
                    <div class="flex flex-col gap-2">
                        <form action="{{ route('leads.proposals.approve', [$leadId, $proposal->id]) }}" method="POST">
                            @csrf
                            <button class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded" onclick="return confirm('Confirmar aprovação? O Lead avançará para Assinatura de Contrato.')">
                                Aprovar Proposta
                            </button>
                        </form>
                        <form action="{{ route('leads.proposals.reject', [$leadId, $proposal->id]) }}" method="POST">
                            @csrf
                            <button class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded" onclick="return confirm('Confirmar rejeição? O Lead será marcado como Perdido.')">
                                Rejeitar Proposta
                            </button>
                        </form>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <div class="mt-10 border-t pt-4">
            <a href="{{ route('leads.proposals.index', $leadId) }}" class="text-indigo-600 hover:text-indigo-800">← Voltar para Lista de Propostas</a>
        </div>
    </div>
</div>
@endsection