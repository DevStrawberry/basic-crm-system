<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Contrato Assinado</title>
</head>
<body class="bg-gray-100 font-sans">
<div class="max-w-lg mx-auto bg-white p-8 rounded-xl shadow-lg mt-10 border border-gray-200">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">Contrato Assinado</h2>

    <p class="text-gray-700 mb-4">
        Olá, {{ $contract->lead->client->name }}!
    </p>

    <p class="text-gray-700 mb-4">
        Informamos que o contrato <strong>#{{ $contract->contract_number }}</strong> foi assinado com sucesso.
    </p>

    <div class="bg-gray-100 border border-gray-300 px-5 py-3 rounded-xl mb-4">
        <p class="text-gray-900"><strong>Número do Contrato:</strong> {{ $contract->contract_number }}</p>
        <p class="text-gray-900"><strong>Valor:</strong> R$ {{ number_format($contract->final_value, 2, ',', '.') }}</p>
        <p class="text-gray-900"><strong>Status:</strong> {{ $contract->status }}</p>
        @if($contract->deadline)
            <p class="text-gray-900"><strong>Prazo:</strong> {{ $contract->deadline->format('d/m/Y') }}</p>
        @endif
    </div>

    <p class="text-gray-700 mb-4">
        O PDF do contrato está anexado a este e-mail para seus registros.
    </p>

    <p class="text-gray-500 text-sm mt-6">
        Caso tenha dúvidas, entre em contato conosco.
    </p>
</div>
</body>
</html>

