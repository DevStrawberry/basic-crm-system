<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Senha Temporária</title>
</head>
<body class="bg-gray-100 font-sans">
<div class="max-w-lg mx-auto bg-white p-8 rounded-xl shadow-lg mt-10 border border-gray-200">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">Olá, {{ $user->name }}!</h2>

    <p class="text-gray-700 mb-4">
        Uma nova senha temporária foi gerada para sua conta no CRM.
        Utilize esta senha para acessar o sistema e, em seguida, altere-a.
    </p>

    <div class="bg-gray-100 border border-gray-300 px-5 py-3 rounded-xl text-center mb-6 font-mono text-lg text-gray-900">
        {{ $tempPassword }}
    </div>

    <p class="text-gray-700 mb-4">
        Após o login, acesse a página de <strong>Alterar Senha</strong> para definir uma senha definitiva.
    </p>

    <a href="{{ route('auth.login') }}" class="inline-block w-full text-center bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-3 rounded-xl shadow-md transition">
        Ir para o Login
    </a>

    <p class="text-gray-500 text-sm mt-6">
        Se você não solicitou essa senha, ignore este e-mail.
    </p>
</div>
</body>
</html>

