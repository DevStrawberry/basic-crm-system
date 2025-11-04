@extends('layouts.app')

@section('title', 'CRM - Clientes')

@section('content')
    <div class="w-full max-w-6xl mx-auto bg-white rounded-3xl shadow-2xl p-8 border border-gray-200">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-3xl font-extrabold text-gray-900">Usuários</h2>
            <a href="{{ route('admin.users.create') }}"
               class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-3 rounded-xl shadow-md transition">
                + Adicionar Usuário
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-5 py-3 rounded-xl mb-6 text-sm font-medium">
                {{ session('success') }}
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
                    <td class="px-6 py-4">{{ $user->status == 0 ? 'Inativo' : 'Ativo' }}</td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <a href="{{ route('admin.users.edit', $user) }}"
                           class="text-yellow-600 hover:text-yellow-800 font-semibold">Editar</a>
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline">
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
                    <td colspan="5" class="px-6 py-6 text-center text-gray-500 italic">
                        Nenhum usuário cadastrado.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection
