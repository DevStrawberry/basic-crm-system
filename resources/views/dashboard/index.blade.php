@extends('layouts.app')

@section('title', 'CRM - Dashboard')

@section('content')
    <div class="w-full max-w-6xl mx-auto bg-white rounded-3xl shadow-2xl p-10 border border-gray-200">
        <h2 class="text-3xl font-extrabold text-gray-900 mb-10 text-center">Dashboard</h2>

        {{-- Cards principais --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            {{-- Clientes --}}
            <a href="{{ route('clients.index') }}"
               class="group bg-gradient-to-br from-indigo-50 to-white border border-indigo-200 rounded-2xl shadow-md hover:shadow-xl transition transform hover:-translate-y-1 p-8 flex flex-col items-center text-center">
                <div class="bg-indigo-100 text-indigo-600 rounded-full p-4 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M5.121 17.804A3 3 0 017 17h10a3 3 0 011.879.804L21 20H3l2.121-2.196zM7 10a4 4 0 118 0 4 4 0 01-8 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Clientes</h3>
                <p class="text-gray-500 text-sm mb-4">Gerencie seus clientes e informações de contato.</p>
                <span class="text-indigo-600 font-semibold group-hover:underline">Acessar</span>
            </a>

            {{-- Leads --}}
            <a href="{{ route('leads.index') }}"
               class="group bg-gradient-to-br from-indigo-50 to-white border border-indigo-200 rounded-2xl shadow-md hover:shadow-xl transition transform hover:-translate-y-1 p-8 flex flex-col items-center text-center">
                <div class="bg-indigo-100 text-indigo-600 rounded-full p-4 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 7a4 4 0 01-8 0M12 14v7m-5 0h10" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Leads</h3>
                <p class="text-gray-500 text-sm mb-4">Acompanhe e converta seus leads em clientes.</p>
                <span class="text-indigo-600 font-semibold group-hover:underline">Acessar</span>
            </a>

            {{-- Relatórios --}}
            <a href="{{ route('reports.index') }}"
               class="group bg-gradient-to-br from-indigo-50 to-white border border-indigo-200 rounded-2xl shadow-md hover:shadow-xl transition transform hover:-translate-y-1 p-8 flex flex-col items-center text-center">
                <div class="bg-indigo-100 text-indigo-600 rounded-full p-4 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 17v-6a2 2 0 012-2h2a2 2 0 012 2v6m4 0V9a2 2 0 00-2-2H7a2 2 0 00-2 2v8m14 0H5" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Relatórios</h3>
                <p class="text-gray-500 text-sm mb-4">Visualize métricas e desempenho das suas vendas.</p>
                <span class="text-indigo-600 font-semibold group-hover:underline">Acessar</span>
            </a>
        </div>
    </div>
@endsection

