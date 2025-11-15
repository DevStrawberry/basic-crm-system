@extends('layouts.app')

@section('title', 'CRM - Visualizar Cliente')

@section('content')
    <div class="flex flex-col w-full max-w-2xl mx-auto">
        <div class="w-full max-w-2xl mx-auto bg-white rounded-3xl shadow-2xl p-10 border border-gray-200 mb-8">
            <h2 class="text-3xl font-extrabold text-center text-gray-900 mb-10">{{ $client->name }}</h2>

            {{-- CPF e Nome --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">CPF</label>
                    <p class="w-full border-2 border-gray-300 rounded-xl px-5 py-3 shadow-sm bg-gray-50">
                        {{ preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $client->cpf) }}
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nome</label>
                    <p class="w-full border-2 border-gray-300 rounded-xl px-5 py-3 shadow-sm bg-gray-50">{{ $client->name }}</p>
                </div>
            </div>

            {{-- Email e Telefone --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">E-mail</label>
                    <p class="w-full border-2 border-gray-300 rounded-xl px-5 py-3 shadow-sm bg-gray-50">{{ $client->email }}</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Telefone</label>
                    <p class="w-full border-2 border-gray-300 rounded-xl px-5 py-3 shadow-sm bg-gray-50">
                        {{ preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $client->phone) }}
                    </p>
                </div>
            </div>

            {{-- Endereço, Cidade, Estado --}}
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Endereço</label>
                <p class="w-full border-2 border-gray-300 rounded-xl px-5 py-3 shadow-sm bg-gray-50">{{ $client->address }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Cidade</label>
                    <p class="w-full border-2 border-gray-300 rounded-xl px-5 py-3 shadow-sm bg-gray-50">{{ $client->city }}</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Estado</label>
                    <p class="w-full border-2 border-gray-300 rounded-xl px-5 py-3 shadow-sm bg-gray-50">{{ $client->state }}</p>
                </div>
            </div>

            {{-- Redes Sociais --}}
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Redes Sociais</label>
                @if ($client->socialNetworks->isNotEmpty())
                    <ul class="space-y-2">
                        @foreach($client->socialNetworks as $social_network)
                            <li class="flex items-center justify-between bg-gray-100 px-4 py-2 rounded-xl shadow-sm">
                                <span>{{ $social_network->name }}:
                                    <a href="{{ $social_network->pivot->profile_url }}" target="_blank" class="text-indigo-600 hover:underline">
                                        {{ $social_network->pivot->profile_url }}
                                    </a>
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-gray-500">Nenhuma rede social cadastrada.</p>
                @endif
            </div>

            {{-- Origem do Contato e Data de Cadastro --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Origem do Contato</label>
                    <p class="w-full border-2 border-gray-300 rounded-xl px-5 py-3 shadow-sm bg-gray-50">
                        {{ $client->contactSource->description }}
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Data de Cadastro</label>
                    <p class="w-full border-2 border-gray-300 rounded-xl px-5 py-3 shadow-sm bg-gray-50">
                        {{ date_format($client->created_at, 'd/m/y') }}
                    </p>
                </div>
            </div>

            {{-- Botões --}}
            <div class="flex items-center justify-between mb-3">
                {{-- Voltar --}}
                <div class="space-x-3">
                    <a href="{{ route('clients.index') }}"
                       class="inline-block mt-4 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-3 px-6 rounded-xl shadow-lg transition cursor-pointer">
                        Voltar
                    </a>
                </div>

                {{-- Editar --}}
                <div class="space-x-3">
                    <a href="{{ route('clients.edit', $client->id) }}"
                       class="inline-block mt-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg transition cursor-pointer">
                        Editar
                    </a>
                </div>

                {{-- Excluir --}}
                <div class="space-x-3">
                    <form action="{{ route('clients.destroy', $client->id) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                onclick="return confirm('Tem certeza que deseja excluir este cliente?')"
                                class="inline-block mt-4 bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg transition cursor-pointer">
                            Excluir
                        </button>
                    </form>
                </div>

                {{-- Cadastrar Lead --}}
                <div class="space-x-3">
                    <a href="{{ route('leads.create', ['client_id' => $client->id]) }}"
                       class="inline-block mt-4 bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg transition cursor-pointer">
                        Cadastrar Lead
                    </a>
                </div>
            </div>
        </div>

        {{-- Leads --}}
        <div class="w-full max-w-2xl mx-auto bg-white rounded-3xl shadow-2xl p-10 border border-gray-200">
            <h3 class="text-2xl font-bold mb-4">Leads Cadastradas</h3>
            @if($client->leads->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full text-left border border-gray-200 rounded-xl shadow-sm">
                        <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 border-b">Título</th>
                            <th class="px-4 py-2 border-b">Status</th>
                            <th class="px-4 py-2 border-b">Data de Criação</th>
                            <th class="px-4 py-2 border-b">Ações</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($client->leads as $lead)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2 border-b">{{ $lead->title }}</td>
                                <td class="px-4 py-2 border-b">{{ $lead->status }}</td>
                                <td class="px-4 py-2 border-b">{{ $lead->created_at->format('d/m/Y') }}</td>
                                <td class="px-4 py-2 border-b space-x-2">
                                    <a href="{{ route('leads.show', $lead->id) }}" class="text-indigo-600 hover:underline cursor-pointer">Ver</a>
                                    <a href="{{ route('leads.edit', $lead->id) }}" class="text-green-600 hover:underline cursor-pointer">Editar</a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500">Nenhuma lead cadastrada para este cliente.</p>
            @endif
        </div>
    </div>
@endsection
