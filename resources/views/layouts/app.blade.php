<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'CRM - Sistema de Funil de Vendas')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- Fonte: Se estiver usando o Google Fonts, você pode incluir um link aqui. Ex: Montserrat ou Poppins --}}
</head>
<body class="bg-gray-100 min-h-screen flex flex-col antialiased">

    {{-- Navbar --}}
    <nav class="bg-white shadow-lg p-4 lg:px-8 flex justify-between items-center sticky top-0 z-50">
        <h1 class="text-3xl font-extrabold text-indigo-700 tracking-tight">Vendas CRM</h1>

        @auth
            <div class="flex items-center space-x-6">
                <a href="" class="text-gray-600 hover:text-indigo-600 font-semibold transition duration-200 ease-in-out">
                    Leads
                </a>
                <form method="POST" action="" class="inline">
                    @csrf
                    <button type="submit" class="text-red-500 hover:text-red-700 font-semibold transition duration-200 ease-in-out bg-transparent border-none p-0 cursor-pointer">
                        Sair
                    </button>
                </form>
            </div>
        @endauth
    </nav>

    {{-- Conteúdo principal com mais respiro --}}
    <main class="grow container mx-auto px-4 py-12 md:py-16 flex justify-center items-start">
        @yield('content')
    </main>

    {{-- Footer Simples --}}
    <footer class="bg-white border-t border-gray-200 text-center py-5 text-sm text-gray-500">
        © {{ date('Y') }} <span class="font-medium text-indigo-600">Vendas CRM</span> - Sistema de Gestão de Funil de Vendas
    </footer>
    </body>
</html>
