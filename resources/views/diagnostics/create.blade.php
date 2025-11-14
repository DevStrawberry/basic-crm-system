@extends('layouts.app')

@section('title', 'Registrar Diagnóstico para Lead #{{ $leadId }}')

@section('content')
    <div class="container mx-auto p-4">
        <div class="bg-white p-6 rounded-lg shadow-xl max-w-2xl mx-auto">
            <h1 class="text-2xl font-bold mb-6 text-gray-800">Registrar Novo Diagnóstico</h1>
            <p class="mb-4 text-gray-600">Lead ID: **{{ $leadId }}**</p>

            <form action="{{ route('leads.diagnostics.store', $leadId) }}" method="POST">
                @csrf

                <div class="space-y-4">
                    {{-- Campo: Descrição do Problema --}}
                    <div>
                        <label for="problem_description" class="block text-sm font-medium text-gray-700">Descrição do Problema</label>
                        <textarea name="problem_description" id="problem_description" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-3 focus:ring-indigo-500 focus:border-indigo-500 @error('problem_description') border-red-500 @enderror">{{ old('problem_description') }}</textarea>
                        @error('problem_description')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Campo: Necessidades do Cliente --}}
                    <div>
                        <label for="customer_needs" class="block text-sm font-medium text-gray-700">Necessidades do Cliente</label>
                        <textarea name="customer_needs" id="customer_needs" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-3 focus:ring-indigo-500 focus:border-indigo-500 @error('customer_needs') border-red-500 @enderror">{{ old('customer_needs') }}</textarea>
                        @error('customer_needs')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Campo: Soluções Possíveis --}}
                    <div>
                        <label for="possible_solutions" class="block text-sm font-medium text-gray-700">Soluções Possíveis</label>
                        <textarea name="possible_solutions" id="possible_solutions" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-3 focus:ring-indigo-500 focus:border-indigo-500 @error('possible_solutions') border-red-500 @enderror">{{ old('possible_solutions') }}</textarea>
                        @error('possible_solutions')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Campo: Nível de Urgência --}}
                    <div>
                        <label for="urgency_level" class="block text-sm font-medium text-gray-700">Nível de Urgência</label>
                        <select name="urgency_level" id="urgency_level" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-3 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="Baixa" {{ old('urgency_level') == 'Baixa' ? 'selected' : '' }}>Baixa</option>
                            <option value="Média" {{ old('urgency_level') == 'Média' ? 'selected' : '' }}>Média</option>
                            <option value="Alta" {{ old('urgency_level') == 'Alta' ? 'selected' : '' }}>Alta</option>
                        </select>
                    </div>
                </div>

                <div class="mt-6 flex justify-between items-center">
                    <a href="{{ route('leads.diagnostics.index', $leadId) }}" class="text-gray-500 hover:text-gray-700">Cancelar</a>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-300">
                        Salvar Diagnóstico
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
