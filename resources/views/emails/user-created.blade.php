<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Seu Usuário foi Criado</title>
</head>
<body class="bg-gray-100 font-sans">
<div class="max-w-lg mx-auto bg-white p-8 rounded-xl shadow-lg mt-10 border border-gray-200">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">Olá, {{ $user->name }}!</h2>

    <p class="text-gray-700 mb-4">
        Sua conta no CRM foi criada com sucesso. Seguem suas credenciais para acesso:
    </p>

    <div class="bg-gray-100 border border-gray-300 px-5 py-3 rounded-xl mb-4 font-mono text-lg text-gray-900">
        <p><strong>E-mail:</strong> {{ $user->email }}</p>
        <p><strong>Senha:</strong> {{ $password }}</p>
    </div>

    <p class="text-gray-700 mb-4">
        Recomendamos que altere sua senha após o primeiro login.
    </p>

    <a href="{{ route('auth.login') }}" class="inline-block w-full text-center bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-3 rounded-xl shadow-md transition">
        Ir para o Login
    </a>

    <p class="text-gray-500 text-sm mt-6">
        Caso não tenha solicitado esta conta, ignore este e-mail.
    </p>
</div>
</body>
</html>
