<!DOCTYPE html>
<html>
<head>
    <title>Proposta Comercial</title>
</head>
<body>
    <p>Prezado(a) {{ $proposal->lead->client->name }},</p>
    
    <p>Segue em anexo a proposta comercial referente a: <strong>{{ $proposal->title }}</strong>.</p>
    
    <p>Ficamos à disposição para dúvidas.</p>
    
    <p>Atenciosamente,<br>
    Equipe Comercial</p>
</body>
</html>