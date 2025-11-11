@extends('layouts.app')

@section('title', 'CRM - Clientes')

@section('content')
    <div class="w-full max-w-6xl mx-auto bg-white rounded-3xl shadow-2xl p-8 border border-gray-200">
        {{-- Cabeçalho --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 space-y-4 md:space-y-0">
            <h2 class="text-3xl font-extrabold text-gray-900">Usuários</h2>
            <div class="flex items-center space-x-3">
                <span>Filtros: </span>
                <form method="GET" action="{{ route('admin.users.index') }}" class="flex items-center space-x-2">
                    {{-- Campo de busca --}}
                    <input type="text" name="search" placeholder="Digite nome ou e-mail"
                           value="{{ request('search') }}"
                           class="rounded-xl border-gray-300 text-gray-700 text-sm px-4 py-2 focus:ring-indigo-500 focus:border-indigo-500">

                    {{-- Filtro por status --}}
                    <select name="status" onchange="this.form.submit()"
                            class="rounded-xl border-gray-300 text-gray-700 text-sm px-4 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Ativo</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inativo</option>
                    </select>

                    {{-- Filtro por perfil (role) --}}
                    <select name="role_id" onchange="this.form.submit()"
                            class="rounded-xl border-gray-300 text-gray-700 text-sm px-4 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Perfil</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                </form>

                {{-- Botão adicionar usuário --}}
                <a href="{{ route('admin.users.create') }}"
                   class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-3 rounded-xl shadow-md transition">
                    + Adicionar Usuário
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
                <th class="px-6 py-3 text-left text-sm font-semibold">#</th>
                <th class="px-6 py-3 text-left text-sm font-semibold">Nome</th>
                <th class="px-6 py-3 text-left text-sm font-semibold">E-mail</th>
                <th class="px-6 py-3 text-left text-sm font-semibold">Perfil</th>
                <th class="px-6 py-3 text-left text-sm font-semibold">Status</th>
                <th class="px-6 py-3 text-right text-sm font-semibold">Ações</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 text-gray-800">
            @forelse($users as $user)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">{{ $user->id }}</td>
                    <td class="px-6 py-4 font-medium">{{ $user->name }}</td>
                    <td class="px-6 py-4">{{ $user->email }}</td>
                    <td class="px-6 py-4">{{ $user->role['name'] }}</td>
                    <td class="px-6 py-4">
                        <span class="{{ $user->status == 'inactive' ? 'text-red-600 font-semibold' : 'text-green-600 font-semibold' }}">
                            {{ $user->status == 'inactive' ? 'Inativo' : 'Ativo' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <a href="{{ route('admin.users.edit', $user->id) }}"
                           class="text-yellow-600 hover:text-yellow-800 font-semibold">Editar</a>
                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    onclick="return confirm('Tem certeza que deseja excluir este usuário? Essa ação não pode ser desfeita')"
                                    class="text-red-600 hover:text-red-800 font-semibold cursor-pointer">Excluir</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-6 text-center text-gray-500 italic">
                        Nenhum usuário encontrado.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>

        {{-- Paginação --}}
        <div class="mt-6">
            {{ $users->appends(request()->query())->links() }}
        </div>
    </div>
@endsection
