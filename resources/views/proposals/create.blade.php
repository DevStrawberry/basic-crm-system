@extends('layouts.app')

@section('title', 'Nova Proposta')

@section('content')
    <div class="container mx-auto p-4">
        <div class="bg-white p-6 rounded-lg shadow-xl max-w-3xl mx-auto">
            <h1 class="text-2xl font-bold mb-6 text-gray-800">Nova Proposta de Valor</h1>
            <p class="mb-4 text-gray-600">Lead ID: <strong>{{ $leadId }}</strong></p>

            <form action="{{ route('leads.proposals.store', $leadId) }}" method="POST">
                @csrf

                <div class="space-y-5">
                    {{-- Título --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Título da Proposta</label>
                        <input type="text" name="title" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-3 focus:ring-indigo-500 focus:border-indigo-500" value="{{ old('title') }}" required placeholder="Ex: Consultoria de Marketing Digital">
                        @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Descrição --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Descrição dos Serviços</label>
                        <textarea name="service_description" rows="4" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-3 focus:ring-indigo-500 focus:border-indigo-500" required>{{ old('service_description') }}</textarea>
                        @error('service_description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Garantias --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Garantias Oferecidas</label>
                        <textarea name="warranties" rows="2" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-3 focus:ring-indigo-500 focus:border-indigo-500" required>{{ old('warranties') }}</textarea>
                        @error('warranties') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Valor --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Valor Total (R$)</label>
                            <input type="number" step="0.01" name="total_value" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-3 focus:ring-indigo-500 focus:border-indigo-500" value="{{ old('total_value') }}" required>
                            @error('total_value') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Validade --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Válida Até</label>
                            <input type="date" name="valid_until" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-3 focus:ring-indigo-500 focus:border-indigo-500" value="{{ old('valid_until') }}" required>
                            @error('valid_until') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Obs --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Observações Internas (Opcional)</label>
                        <textarea name="notes" rows="2" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-3 focus:ring-indigo-500 focus:border-indigo-500">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <div class="mt-8 flex justify-between items-center">
                    <a href="{{ route('leads.proposals.index', $leadId) }}" class="text-gray-500 hover:text-gray-700">Cancelar</a>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-lg shadow-md transition duration-300">
                        Salvar Rascunho
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection