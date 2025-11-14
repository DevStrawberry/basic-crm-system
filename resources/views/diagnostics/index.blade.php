@extends('layouts.app')

@section('title', 'Diagnósticos do Lead #{{ $leadId }}')

@section('content')
    <div class="container mx-auto p-4">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Diagnósticos do Lead #{{ $leadId }}</h1>
            <a href="{{ route('leads.diagnostics.create', $leadId) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-300">
                + Registrar Novo Diagnóstico
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if($diagnostics->isEmpty())
            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4" role="alert">
                <p>Nenhum diagnóstico registrado para este Lead ainda.</p>
            </div>
        @else
            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assessor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nível de Urgência</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descrição do Problema (Início)</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Criado em</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($diagnostics as $diagnostic)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $diagnostic->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $diagnostic->diagnosedBy->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($diagnostic->urgency_level === 'Alta') bg-red-100 text-red-800
                                    @elseif($diagnostic->urgency_level === 'Média') bg-yellow-100 text-yellow-800
                                    @else bg-green-100 text-green-800
                                    @endif">
                                    {{ $diagnostic->urgency_level }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ Str::limit($diagnostic->problem_description, 50) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $diagnostic->created_at->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('leads.diagnostics.show', ['lead_id' => $leadId, 'diagnostic' => $diagnostic->id]) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Ver</a>
                                <a href="{{ route('leads.diagnostics.edit', ['lead_id' => $leadId, 'diagnostic' => $diagnostic->id]) }}" class="text-yellow-600 hover:text-yellow-900">Editar</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <div class="mt-4">
            <a href="{{ route('leads.show', $leadId) }}" class="text-indigo-600 hover:text-indigo-800 text-sm">← Voltar para o Lead</a>
        </div>
    </div>
@endsection
