@extends('layouts.app')

@section('title', 'CRM - Resetar Senha')

@section('content')
    <div class="w-full max-w-lg mx-auto bg-white rounded-3xl shadow-2xl p-10 border border-gray-200">
        <h2 class="text-3xl font-extrabold text-center text-gray-900 mb-10">Resetar Senha</h2>

        {{-- Mensagem de sucesso --}}
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        {{-- Mensagens de erro --}}
        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $erro)
                        <li>{{ $erro }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('auth.reset.password.send.email') }}" class="space-y-6">
            @csrf

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">E-mail</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       class="w-full border-2 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl px-5 py-3">
                @error('email')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded-xl shadow-lg transition">
                Enviar Link de Reset de Senha
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-gray-600">
            Voltar para tela de login
            <a href="{{ route('auth.login') }}" class="text-indigo-600 hover:text-indigo-700 font-semibold">Faça login</a>
        </p>
    </div>
@endsection
