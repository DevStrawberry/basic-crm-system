@extends('layouts.app')

@section('title', 'CRM - Visualizar Contrato')

@section('content')
<div class="min-h-screen bg-gray-50 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto space-y-6">
        {{-- Header Card --}}
        <div class="bg-white shadow-lg rounded-xl p-6 border border-gray-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900">Contrato #{{ $contract->contract_number }}</h2>
                    <p class="mt-1 text-sm text-gray-500">Cliente: {{ $contract->lead->client->name }}</p>
                </div>
                <a href="{{ route('leads.contract.index', $leadId) }}"
                   class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Voltar
                </a>
            </div>
        </div>

        {{-- Messages --}}
        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3-7a1 1 0 11-2 0v-4a1 1 0 011-1h4a1 1 0 110 2h-3v4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Erro ao processar</h3>
                        <ul class="mt-2 text-sm text-red-700 list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Main Info Card --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Detalhes do Contrato --}}
                <div class="bg-white shadow-lg rounded-xl border border-gray-200 overflow-hidden">
                    <div class="bg-indigo-50 px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-indigo-900">Detalhes do Contrato</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Cliente</label>
                                <p class="text-sm font-medium text-gray-900">{{ $contract->lead->client->name }}</p>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Status</label>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                                    @if($contract->status == 'Assinado') bg-green-100 text-green-800
                                    @elseif($contract->status == 'Fechado') bg-blue-100 text-blue-800
                                    @elseif($contract->status == 'Cancelado') bg-red-100 text-red-800
                                    @elseif($contract->status == 'Aguardando Assinatura') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ $contract->status }}
                                </span>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Valor Final</label>
                                <p class="text-sm font-medium text-gray-900">R$ {{ number_format($contract->final_value, 2, ',', '.') }}</p>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Método de Pagamento</label>
                                <p class="text-sm font-medium text-gray-900">{{ $contract->payment_method }}</p>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Prazo</label>
                                <p class="text-sm font-medium text-gray-900">{{ $contract->deadline ? $contract->deadline->format('d/m/Y') : 'Não definido' }}</p>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Assessor Responsável</label>
                                <p class="text-sm font-medium text-gray-900">{{ $contract->assignedTo ? $contract->assignedTo->name : 'Não atribuído' }}</p>
                            </div>

                            @if($contract->signed_at)
                                <div class="sm:col-span-2">
                                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Assinado por</label>
                                    <p class="text-sm font-medium text-gray-900">{{ $contract->signed_by }} em {{ $contract->signed_at->format('d/m/Y H:i') }}</p>
                                </div>
                            @endif
                        </div>

                        @if($contract->notes)
                            <div class="mt-6 pt-6 border-t border-gray-200">
                                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Notas</label>
                                <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $contract->notes }}</p>
                            </div>
                        @endif

                        {{-- Anexos --}}
                        @if($contract->attachments->count() > 0)
                            <div class="mt-6 pt-6 border-t border-gray-200">
                                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-3">Anexos</label>
                                <div class="space-y-2">
                                    @foreach($contract->attachments as $attachment)
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200 hover:bg-gray-100 transition">
                                            <div class="flex items-center">
                                                <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                                </svg>
                                                <span class="text-sm text-gray-700">{{ $attachment->filename }}</span>
                                            </div>
                                            <a href="{{ asset('storage/' . $attachment->file_path) }}" target="_blank"
                                               class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">Download</a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Interações --}}
                <div class="bg-white shadow-lg rounded-xl border border-gray-200 overflow-hidden">
                    <div class="bg-indigo-50 px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-indigo-900">Interações</h3>
                    </div>
                    <div class="p-6">
                        @if($contract->interactions->count() > 0)
                            <div class="space-y-4">
                                @foreach($contract->interactions as $interaction)
                                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2 mb-2">
                                            <div class="flex items-center gap-2">
                                                <span class="font-semibold text-gray-900">{{ $interaction->subject }}</span>
                                                <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded-full font-medium">
                                                    {{ $interaction->type }}
                                                </span>
                                            </div>
                                            <span class="text-xs text-gray-500">
                                                {{ $interaction->happened_at->format('d/m/Y H:i') }}
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-700 mb-2">{{ $interaction->body }}</p>
                                        <p class="text-xs text-gray-500">Por: {{ $interaction->createdBy->name }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500 italic text-center py-8">Nenhuma interação registrada.</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Actions Sidebar --}}
            <div class="space-y-6">
                <div class="bg-white shadow-lg rounded-xl border border-gray-200 overflow-hidden">
                    <div class="bg-indigo-50 px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-indigo-900">Ações</h3>
                    </div>
                    <div class="p-4 space-y-3">
                        @if(strtolower(auth()->user()->role->name ?? '') === 'gestor')
                            <a href="{{ route('leads.contract.edit', ['lead_id' => $leadId, 'contract' => $contract->id]) }}"
                               class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Editar
                            </a>
                            <button onclick="showAssignModal()"
                                    class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                                Atribuir Assessor
                            </button>
                        @endif

                        @if($contract->status !== 'Assinado' && $contract->status !== 'Fechado')
                            <button onclick="showSignModal()"
                                    class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Assinar Contrato
                            </button>
                        @endif

                        <button onclick="showInteractModal()"
                                class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Registrar Interação
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal: Registrar Interação --}}
    <div id="interactModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full transform transition-all">
            <div class="bg-indigo-50 px-6 py-4 border-b border-gray-200 rounded-t-xl">
                <h3 class="text-xl font-bold text-indigo-900">Registrar Interação</h3>
            </div>
            <form method="POST" action="{{ route('leads.contract.interact', ['lead_id' => $leadId, 'contract' => $contract->id]) }}" class="p-6">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tipo</label>
                        <select name="type" required class="w-full rounded-lg border-gray-300 px-4 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="Ligação">Ligação</option>
                            <option value="E-mail">E-mail</option>
                            <option value="Reunião">Reunião</option>
                            <option value="Mensagem">Mensagem</option>
                            <option value="Nota">Nota</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Assunto</label>
                        <input type="text" name="subject" required class="w-full rounded-lg border-gray-300 px-4 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Descrição</label>
                        <textarea name="body" rows="4" required class="w-full rounded-lg border-gray-300 px-4 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Data</label>
                        <input type="datetime-local" name="happened_at" value="{{ now()->format('Y-m-d\TH:i') }}" class="w-full rounded-lg border-gray-300 px-4 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>
                <div class="flex gap-3 mt-6">
                    <button type="button" onclick="hideInteractModal()" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">Cancelar</button>
                    <button type="submit" class="flex-1 px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">Salvar</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal: Atribuir Assessor --}}
    <div id="assignModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full transform transition-all">
            <div class="bg-purple-50 px-6 py-4 border-b border-gray-200 rounded-t-xl">
                <h3 class="text-xl font-bold text-purple-900">Atribuir Assessor</h3>
            </div>
            <form method="POST" action="{{ route('leads.contract.assign', ['lead_id' => $leadId, 'contract' => $contract->id]) }}" class="p-6">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Assessor</label>
                    <select name="assigned_to" required class="w-full rounded-lg border-gray-300 px-4 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        @foreach(\App\Models\User::whereHas('role', fn($q) => $q->where('name', 'Assessor'))->get() as $assessor)
                            <option value="{{ $assessor->id }}" {{ $contract->assigned_to == $assessor->id ? 'selected' : '' }}>
                                {{ $assessor->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-3 mt-6">
                    <button type="button" onclick="hideAssignModal()" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">Cancelar</button>
                    <button type="submit" class="flex-1 px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors">Atribuir</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal: Assinar Contrato --}}
    <div id="signModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full transform transition-all">
            <div class="bg-green-50 px-6 py-4 border-b border-gray-200 rounded-t-xl">
                <h3 class="text-xl font-bold text-green-900">Assinar Contrato</h3>
            </div>
            <form method="POST" action="{{ route('leads.contract.sign', ['lead_id' => $leadId, 'contract' => $contract->id]) }}" class="p-6">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Assinado por</label>
                        <input type="text" name="signed_by" required class="w-full rounded-lg border-gray-300 px-4 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500" placeholder="Nome do signatário">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Mover Lead</label>
                        <select name="move_lead_status" class="w-full rounded-lg border-gray-300 px-4 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                            <option value="">Não mover</option>
                            <option value="client">Marcar como Cliente (Vencido)</option>
                            <option value="lost">Marcar como Perdido</option>
                        </select>
                    </div>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                        <p class="text-sm text-blue-800">O sistema irá gerar o PDF, solicitar assinatura digital e enviar email automaticamente.</p>
                    </div>
                </div>
                <div class="flex gap-3 mt-6">
                    <button type="button" onclick="hideSignModal()" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">Cancelar</button>
                    <button type="submit" class="flex-1 px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">Assinar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showInteractModal() {
            document.getElementById('interactModal').classList.remove('hidden');
        }
        function hideInteractModal() {
            document.getElementById('interactModal').classList.add('hidden');
        }
        function showAssignModal() {
            document.getElementById('assignModal').classList.remove('hidden');
        }
        function hideAssignModal() {
            document.getElementById('assignModal').classList.add('hidden');
        }
        function showSignModal() {
            document.getElementById('signModal').classList.remove('hidden');
        }
        function hideSignModal() {
            document.getElementById('signModal').classList.add('hidden');
        }
        
        // Fechar modal ao clicar fora
        document.addEventListener('click', function(event) {
            const modals = ['interactModal', 'assignModal', 'signModal'];
            modals.forEach(modalId => {
                const modal = document.getElementById(modalId);
                if (event.target === modal) {
                    modal.classList.add('hidden');
                }
            });
        });
    </script>
</div>
@endsection
