@extends('layouts.app')

@section('title', 'Diagnóstico #{{ $diagnostic->id }}')

@section('content')
    <div class="container mx-auto p-4">
        <div class="bg-white p-8 rounded-lg shadow-2xl max-w-4xl mx-auto">
            <div class="flex justify-between items-start mb-8 border-b pb-4">
                <div>
                    <h1 class="text-3xl font-extrabold text-indigo-700">Diagnóstico #{{ $diagnostic->id }}</h1>
                    <p class="text-lg text-gray-500">
                        Lead: <a href="{{ route('leads.show', $leadId) }}" class="text-indigo-600 hover:text-indigo-800 font-semibold">{{ $diagnostic->lead->name ?? 'Lead Desconhecido' }}</a>
                    </p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('leads.diagnostics.edit', ['lead_id' => $leadId, 'diagnostic' => $diagnostic->id]) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded-lg shadow transition duration-300">
                        Editar
                    </a>
                    <form action="{{ route('leads.diagnostics.destroy', ['lead_id' => $leadId, 'diagnostic' => $diagnostic->id]) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja deletar este diagnóstico? Esta ação não pode ser desfeita.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg shadow transition duration-300">
                            Deletar
                        </button>
                    </form>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- Coluna de Detalhes --}}
                <div>
                    <h2 class="text-xl font-semibold mb-3 text-gray-700">Informações Básicas</h2>
                    <div class="space-y-3">
                        <p class="text-sm">
                            <span class="font-medium text-gray-600">Assessor Responsável:</span>
                            <span class="text-gray-900">{{ $diagnostic->diagnosedBy->name ?? 'N/A' }}</span>
                        </p>
                        <p class="text-sm">
                            <span class="font-medium text-gray-600">Data do Registro:</span>
                            <span class="text-gray-900">{{ $diagnostic->created_at->format('d/m/Y H:i') }}</span>
                        </p>
                        <p class="text-sm">
                            <span class="font-medium text-gray-600">Nível de Urgência:</span>
                            <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full
                            @if($diagnostic->urgency_level === 'Alta') bg-red-100 text-red-800
                            @elseif($diagnostic->urgency_level === 'Média') bg-yellow-100 text-yellow-800
                            @else bg-green-100 text-green-800
                            @endif">
                            {{ $diagnostic->urgency_level }}
                        </span>
                        </p>
                    </div>
                </div>

                {{-- Coluna de Anexos --}}
                <div>
                    <h2 class="text-xl font-semibold mb-3 text-gray-700">Anexos ({{ $diagnostic->attachments->count() }})</h2>
                    @if($diagnostic->attachments->isEmpty())
                        <p class="text-sm text-gray-500">Nenhum documento anexado.</p>
                    @else
                        <ul class="list-disc ml-5 text-indigo-600">
                            @foreach($diagnostic->attachments as $attachment)
                                <li><a href="#" class="hover:underline">{{ $attachment->file_name }}</a></li>
                            @endforeach
                        </ul>
                    @endif
                    {{-- Botão para anexar (Implementação futura) --}}
                    <button class="mt-4 text-sm text-indigo-500 hover:text-indigo-700">Anexar Novo Documento</button>
                </div>
            </div>

            {{-- Seção de Detalhes do Diagnóstico --}}
            <div class="mt-8 border-t pt-8 space-y-8">
                <div>
                    <h2 class="text-xl font-semibold mb-3 text-gray-700">Descrição do Problema</h2>
                    <div class="bg-gray-50 p-4 rounded-lg text-gray-800 whitespace-pre-line">{{ $diagnostic->problem_description }}</div>
                </div>

                <div>
                    <h2 class="text-xl font-semibold mb-3 text-gray-700">Necessidades do Cliente</h2>
                    <div class="bg-gray-50 p-4 rounded-lg text-gray-800 whitespace-pre-line">{{ $diagnostic->customer_needs }}</div>
                </div>

                <div>
                    <h2 class="text-xl font-semibold mb-3 text-gray-700">Soluções Possíveis</h2>
                    <div class="bg-gray-50 p-4 rounded-lg text-gray-800 whitespace-pre-line">{{ $diagnostic->possible_solutions }}</div>
                </div>
            </div>

            <div class="mt-10">
                <a href="{{ route('leads.diagnostics.index', $leadId) }}" class="text-indigo-600 hover:text-indigo-800 text-sm">← Voltar para a Lista de Diagnósticos</a>
            </div>
        </div>
    </div>
@endsection
