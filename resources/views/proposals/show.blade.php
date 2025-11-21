@extends('layouts.app')

@section('title', 'Detalhes da Proposta #' . $proposal->id)

@section('content')
<div class="container mx-auto p-4">
    {{-- Header Principal --}}
    <div class="bg-white p-8 rounded-lg shadow-2xl max-w-6xl mx-auto mb-6">
        <div class="flex justify-between items-start border-b pb-4 mb-6">
            <div>
                <h1 class="text-3xl font-extrabold text-indigo-700">{{ $proposal->title }}</h1>
                <p class="text-gray-500 mt-1">
                    Status: 
                    <span class="px-3 py-1 rounded-full text-sm font-bold 
                        @if($proposal->status == 'Aceita') bg-green-100 text-green-700 
                        @elseif($proposal->status == 'Rejeitada') bg-red-100 text-red-700 
                        @elseif($proposal->status == 'Enviada') bg-blue-100 text-blue-700
                        @else bg-yellow-100 text-yellow-700 @endif">
                        {{ $proposal->status }}
                    </span>
                </p>
            </div>
            
            @if($proposal->status === 'Draft')
            <div class="flex space-x-2">
                <a href="{{ route('leads.proposals.edit', [$leadId, $proposal->id]) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded shadow transition">
                    Editar
                </a>
                <form action="{{ route('leads.proposals.destroy', [$leadId, $proposal->id]) }}" method="POST" onsubmit="return confirm('Excluir proposta permanentemente?');">
                    @csrf @method('DELETE')
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded shadow transition">
                        Excluir
                    </button>
                </form>
            </div>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Coluna Esquerda: Dados da Proposta --}}
            <div class="lg:col-span-2 space-y-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Descrição dos Serviços</h3>
                    <div class="bg-gray-50 p-4 rounded border border-gray-200 text-gray-800 whitespace-pre-line">{{ $proposal->service_description }}</div>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Garantias</h3>
                    <div class="bg-gray-50 p-4 rounded border border-gray-200 text-gray-800 whitespace-pre-line">{{ $proposal->warranties }}</div>
                </div>

                @if($proposal->notes)
                <div>
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Observações Internas</h3>
                    <div class="text-sm text-gray-600 bg-yellow-50 p-3 rounded border border-yellow-200">{{ $proposal->notes }}</div>
                </div>
                @endif

                {{-- Seção de Anexos (Mantida pois existe no Model) --}}
                <div class="mt-8 border-t pt-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Arquivos Anexados</h3>
                    @if($proposal->attachments->isEmpty())
                        <p class="text-gray-500 text-sm italic">Nenhum arquivo extra anexado.</p>
                    @else
                        <ul class="space-y-2">
                            @foreach($proposal->attachments as $attachment)
                                <li class="flex items-center justify-between bg-gray-50 p-3 rounded border">
                                    <span class="text-sm text-gray-700 truncate">{{ $attachment->filename }}</span>
                                    {{-- Link fictício de download, ajustar conforme sua rota de arquivos --}}
                                    <span class="text-xs text-gray-500">({{ $attachment->created_at->format('d/m/Y') }})</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            {{-- Coluna Direita: Resumo e Ações --}}
            <div class="space-y-6">
                <div class="bg-indigo-50 p-5 rounded-lg border border-indigo-100 shadow-sm">
                    <h3 class="text-indigo-900 font-bold text-lg mb-4">Resumo Comercial</h3>
                    <div class="space-y-2">
                        <p class="flex justify-between text-sm"><span>Valor Total:</span> <span class="font-bold text-lg text-gray-800">R$ {{ number_format($proposal->total_value, 2, ',', '.') }}</span></p>
                        
                        {{-- Correção de Data com Carbon::parse --}}
                        <p class="flex justify-between text-sm"><span>Válida até:</span> <span class="text-gray-700">{{ \Carbon\Carbon::parse($proposal->valid_until)->format('d/m/Y') }}</span></p>
                        
                        <p class="flex justify-between text-sm"><span>Criada por:</span> <span class="text-gray-700">{{ $proposal->createdBy->name ?? 'N/A' }}</span></p>
                        
                        @if($proposal->sent_at)
                            <p class="flex justify-between text-sm pt-2 border-t border-indigo-200 mt-2 text-blue-700 font-semibold"><span>Enviada em:</span> <span>{{ \Carbon\Carbon::parse($proposal->sent_at)->format('d/m/Y') }}</span></p>
                        @endif
                    </div>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <h4 class="text-xs font-bold text-gray-500 uppercase mb-3 tracking-wider">Ações de Documento</h4>
                    <div class="space-y-2">
                        <a href="{{ route('leads.proposals.pdf', [$leadId, $proposal->id]) }}" target="_blank" class="flex items-center justify-center w-full bg-white hover:bg-gray-100 text-gray-700 font-semibold py-2 px-4 rounded border border-gray-300 transition shadow-sm">
                            <span class="mr-2">📄</span> Visualizar PDF
                        </a>
                        
                        <form action="{{ route('leads.proposals.email', [$leadId, $proposal->id]) }}" method="POST">
                            @csrf
                            <button type="submit" class="flex items-center justify-center w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition shadow-sm" onclick="return confirm('Enviar PDF por e-mail para o cliente?')">
                                <span class="mr-2">✉️</span> Enviar por E-mail
                            </button>
                        </form>
                    </div>
                </div>

                @if($proposal->status !== 'Aceita' && $proposal->status !== 'Rejeitada')
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <h4 class="text-xs font-bold text-gray-500 uppercase mb-3 tracking-wider">Decisão do Cliente</h4>
                    <div class="flex flex-col gap-2">
                        <form action="{{ route('leads.proposals.approve', [$leadId, $proposal->id]) }}" method="POST">
                            @csrf
                            <button class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition shadow-sm" onclick="return confirm('Confirmar aprovação?')">
                                ✅ Aprovar Proposta
                            </button>
                        </form>
                        <form action="{{ route('leads.proposals.reject', [$leadId, $proposal->id]) }}" method="POST">
                            @csrf
                            <button class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded transition shadow-sm" onclick="return confirm('Confirmar rejeição?')">
                                ❌ Rejeitar Proposta
                            </button>
                        </form>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <div class="mt-8 max-w-6xl mx-auto border-t pt-4">
            <a href="{{ route('leads.proposals.index', $leadId) }}" class="text-indigo-600 hover:text-indigo-800 font-medium">← Voltar para Lista de Propostas</a>
        </div>
    </div>
</div>
@endsection