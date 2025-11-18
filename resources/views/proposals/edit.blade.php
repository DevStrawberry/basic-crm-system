@extends('layouts.app')

@section('title', 'Editar Proposta')

@section('content')
    <div class="container mx-auto p-4">
        <div class="bg-white p-6 rounded-lg shadow-xl max-w-3xl mx-auto">
            <h1 class="text-2xl font-bold mb-6 text-gray-800">Editar Proposta #{{ $proposal->id }}</h1>

            <form action="{{ route('leads.proposals.update', [$leadId, $proposal->id]) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Título</label>
                        <input type="text" name="title" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-3 focus:ring-indigo-500 focus:border-indigo-500" value="{{ old('title', $proposal->title) }}" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Descrição dos Serviços</label>
                        <textarea name="service_description" rows="4" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-3 focus:ring-indigo-500 focus:border-indigo-500" required>{{ old('service_description', $proposal->service_description) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Garantias</label>
                        <textarea name="warranties" rows="2" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-3 focus:ring-indigo-500 focus:border-indigo-500" required>{{ old('warranties', $proposal->warranties) }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Valor Total (R$)</label>
                            <input type="number" step="0.01" name="total_value" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-3 focus:ring-indigo-500 focus:border-indigo-500" value="{{ old('total_value', $proposal->total_value) }}" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Válida Até</label>
                            <input type="date" name="valid_until" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-3 focus:ring-indigo-500 focus:border-indigo-500" value="{{ old('valid_until', $proposal->valid_until->format('Y-m-d')) }}" required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Observações</label>
                        <textarea name="notes" rows="2" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-3 focus:ring-indigo-500 focus:border-indigo-500">{{ old('notes', $proposal->notes) }}</textarea>
                    </div>
                </div>

                <div class="mt-8 flex justify-between items-center">
                    <a href="{{ route('leads.proposals.show', [$leadId, $proposal->id]) }}" class="text-gray-500 hover:text-gray-700">Cancelar</a>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-lg shadow-md transition duration-300">
                        Atualizar Proposta
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection