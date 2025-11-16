@extends('layouts.app')

@section('title', 'CRM - Leads')

@section('content')
    <div class="w-full max-w-6xl mx-auto bg-white rounded-3xl shadow-2xl p-8 border border-gray-200">
        {{-- Cabeçalho --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 space-y-4 md:space-y-0">
            <h2 class="text-3xl font-extrabold text-gray-900">Leads</h2>
            <div class="flex items-center space-x-3">
                <span>Filtros: </span>
                <form method="GET" action="{{ route('leads.index') }}" class="flex items-center space-x-2 w-full md:w-auto">
                    {{-- Campo de busca por nome do cliente --}}
                    <input type="text" name="search" placeholder="Digite o nome do cliente"
                           value="{{ request('search') }}"
                           class="rounded-xl border-gray-300 text-gray-700 text-sm px-4 py-2 focus:ring-indigo-500 focus:border-indigo-500 w-full md:w-64 min-w-[260px]">

                    {{-- Filtro por Status --}}
                    <select name="status" onchange="this.form.submit()"
                            class="rounded-xl border-gray-300 text-gray-700 text-sm px-4 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Status</option>
                        <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>Nova</option>
                        <option value="on_going" {{ request('status') == 'on_going' ? 'selected' : '' }}>Em Andamento</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Finalizada</option>
                        <option value="lost" {{ request('status') == 'lost' ? 'selected' : '' }}>Perdida</option>
                    </select>

                    {{-- Filtro por Estágio do Pipeline --}}
                    <select name="pipeline_stage" onchange="this.form.submit()"
                            class="rounded-xl border-gray-300 text-gray-700 text-sm px-4 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Estágio do Pipeline</option>
                        @foreach($pipelineStages as $pipelineStage)
                            <option value="{{ $pipelineStage->id }}" {{ request('pipeline_stage') == $pipelineStage->id ? 'selected' : '' }}>
                                {{ $pipelineStage->name }}</option>
                        @endforeach
                        <option value="on_going" {{ request('pipeline_stage') == 'on_going' ? 'selected' : '' }}>Em Andamento</option>
                        <option value="completed" {{ request('pipeline_stage') == 'completed' ? 'selected' : '' }}>Finalizada</option>
                        <option value="lost" {{ request('pipeline_stage') == 'lost' ? 'selected' : '' }}>Perdida</option>
                    </select>
                </form>

                {{-- Botão adicionar lead --}}
                <a href="{{ route('leads.create') }}"
                   class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-3 rounded-xl shadow-md transition cursor-pointer">
                    + Cadastrar nova Lead
                </a>
            </div>
        </div>

        {{-- Mensagens --}}
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-5 py-3 rounded-xl mb-6 text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="text-red-600 mb-4">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <table class="min-w-full border border-gray-200 rounded-xl overflow-hidden">
            <thead class="bg-gray-50 text-gray-700">
            <tr>
                <th class="px-6 py-3 text-left text-sm font-semibold whitespace-nowrap">Título da Lead</th>
                <th class="px-6 py-3 text-left text-sm font-semibold whitespace-nowrap">Cliente</th>
                <th class="px-6 py-3 text-left text-sm font-semibold whitespace-nowrap">Status</th>
                <th class="px-6 py-3 text-left text-sm font-semibold">Estágio do Pipeline</th>
                <th class="px-6 py-3 text-center text-sm font-semibold whitespace-nowrap">Ações</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 text-gray-800">
            @forelse($leads as $lead)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap">{{ $lead->title }}</td>
                    <td class="px-6 py-4 font-medium max-w-[220px] truncate">{{ $lead->client->name }}</td>
                    <td class="px-6 py-4 max-w-[240px] truncate">
                        @switch($lead->status)
                            @case('new') Nova @break
                            @case('on_going') Em Andamento @break
                            @case('completed') Finalizada @break
                            @case('lost') Perdida @break
                        @endswitch
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $lead->pipelineStage->name }}</td>

                    {{-- Icones de ações --}}
                    <td class="px-6 py-4 text-right whitespace-nowrap">
                        <div class="flex items-center justify-end space-x-3">

                            {{-- Visualizar --}}
                            <a href="{{ route('leads.show', $lead->id) }}"
                               class="text-indigo-600 hover:text-indigo-800 cursor-pointer" title="Visualizar">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                     fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                     stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M2.036 12.322a1 1 0 010-.644C3.423 7.51 7.36 4.5 12
                         4.5c4.638 0 8.573 3.007 9.963 7.178.07.2.07.422
                         0 .622C20.577 16.49 16.64 19.5 12
                         19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </a>

                            {{-- Editar --}}
                            <a href="{{ route('leads.edit', $lead->id) }}"
                               class="text-yellow-600 hover:text-yellow-800 cursor-pointer" title="Editar">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                     fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                     stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M16.862 4.487l1.687-1.688a2.121 2.121 0 113
                         3L12 15l-4 1 1-4 7.862-7.513z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M18 14v6H6v-6" />
                                </svg>
                            </a>

                            {{-- Excluir --}}
                            <form action="{{ route('leads.destroy', $lead->id) }}" method="POST"
                                  onsubmit="return confirm('Tem certeza que deseja excluir esta lead? Isso excluirá todos os diagnósticos, propostas, contratos e arquivos relacionados a esta lead. Essa ação não pode ser desfeita')"
                                  class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 cursor-pointer" title="Excluir">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                         fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                         stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M6 7h12M9 7V4h6v3m-7 4v7m4-7v7m4-7v7" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-6 text-center text-gray-500 italic">
                        Nenhuma lead cadastrada.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>

        {{-- Paginação --}}
        <div class="mt-6">
            {{ $leads->appends(request()->query())->links() }}
        </div>
    </div>
@endsection
