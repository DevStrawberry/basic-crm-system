@extends('layouts.app')

@section('title', 'CRM - Novo Cliente')

@section('content')
    <div class="w-full max-w-lg mx-auto bg-white rounded-3xl shadow-2xl p-10 border border-gray-200">
        <h2 class="text-3xl font-extrabold text-center text-gray-900 mb-10">Cadastrar Novo Usuário</h2>

        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-6 space-x-6 justify-center">
            @csrf

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Nome</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       class="w-full border-2 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl px-5 py-3">
                @error('nome') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">E-mail</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       class="w-full border-2 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl px-5 py-3">
                @error('email') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Perfil</label>
                <select name="perfil" required
                        class="w-full border-2 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl px-5 py-3 text-gray-800 shadow-sm">
                    <option value="">Selecione o perfil</option>
                    <option value="Administrador" {{ old('perfil') == 'Administrador' ? 'selected' : '' }}>Administrador</option>
                    <option value="Gestor" {{ old('perfil') == 'Gestor' ? 'selected' : '' }}>Gestor</option>
                    <option value="Assessor" {{ old('perfil') == 'Assessor' ? 'selected' : '' }}>Assessor</option>
                </select>
                @error('perfil')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded-xl shadow-lg transition">
                Cadastrar Usuário
            </button>
        </form>
    </div>
@endsection

