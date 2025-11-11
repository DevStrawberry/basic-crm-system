@extends('layouts.app')

@section('title', 'CRM - Login')

@section('content')
    <div class="w-full max-w-lg bg-white rounded-3xl shadow-2xl p-8 sm:p-10 md:p-12 border border-gray-200 transform hover:shadow-3xl transition duration-500 ease-in-out">
        <h2 class="text-4xl font-extrabold text-center text-gray-900 mb-10 tracking-tight">
            Acesse sua conta
        </h2>

        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-xl border border-red-300">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('auth.authenticate') }}" class="space-y-8 flex flex-col">
            @csrf
            <div class="space-y-2">
                <label for="email" class="block text-sm font-semibold text-gray-700">E-mail</label>
                <input type="email" name="email" id="email" required autofocus
                       placeholder="seu.email@empresa.com"
                       class="w-full border-2 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 focus:ring-1 focus:outline-none rounded-xl px-5 py-3 text-gray-800 transition duration-300 ease-in-out shadow-sm placeholder:text-gray-400">
            </div>

            <div class="space-y-2">
                <label for="password" class="block text-sm font-semibold text-gray-700">Senha</label>
                <input type="password" name="password" id="password" required
                       placeholder="••••••••"
                       class="w-full border-2 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 focus:ring-1 focus:outline-none rounded-xl px-5 py-3 text-gray-800 transition duration-300 ease-in-out shadow-sm">
            </div>

            <div class="flex items-center justify-between text-sm">
                <label class="flex items-center text-gray-600 hover:text-indigo-600 transition">
                    <input type="checkbox" name="remember" class="mr-2 rounded text-indigo-600 border-gray-300 focus:ring-indigo-500">
                    Lembrar-me
                </label>
                <a href="{{ route('auth.reset.password') }}" class="text-indigo-600 font-medium hover:text-indigo-700 hover:underline transition duration-300 ease-in-out">
                    Esqueci minha senha
                </a>
            </div>

            <button type="submit"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded-xl shadow-lg shadow-indigo-200 transition duration-300 ease-in-out transform hover:scale-[1.01]">
                Entrar no Sistema
            </button>
        </form>
    </div>
@endsection
