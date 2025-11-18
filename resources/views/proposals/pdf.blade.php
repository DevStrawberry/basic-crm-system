<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Proposta #{{ $proposal->id }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; }
        .header { text-align: center; margin-bottom: 40px; border-bottom: 2px solid #eee; pb: 20px; }
        .title { font-size: 24px; font-weight: bold; color: #2d3748; }
        .client-info { margin-bottom: 30px; background: #f7fafc; padding: 15px; }
        .section-title { font-size: 16px; font-weight: bold; margin-top: 20px; border-bottom: 1px solid #ccc; padding-bottom: 5px; }
        .content { margin-top: 10px; line-height: 1.6; white-space: pre-line; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 12px; color: #718096; }
        .total-box { float: right; background: #2d3748; color: white; padding: 10px 20px; font-size: 18px; border-radius: 5px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">{{ $proposal->title }}</div>
        <p>Proposta Comercial #{{ $proposal->id }}</p>
    </div>

    <div class="client-info">
        <strong>Cliente:</strong> {{ $proposal->lead->client->name }}<br>
        <strong>Data de Emissão:</strong> {{ date('d/m/Y') }}<br>
        <strong>Válido até:</strong> {{ $proposal->valid_until->format('d/m/Y') }}
    </div>

    <div class="section-title">Descrição dos Serviços</div>
    <div class="content">{{ $proposal->service_description }}</div>

    <div class="section-title">Garantias</div>
    <div class="content">{{ $proposal->warranties }}</div>

    <div class="total-box">
        Total: R$ {{ number_format($proposal->total_value, 2, ',', '.') }}
    </div>

    <div class="footer">
        Gerado automaticamente pelo sistema CRM.
    </div>
</body>
</html>