<h1>Olá {{ $user->name }}!</h1>

<p>Seu usuário foi criado no sistema.</p>

<p>
    Email: {{ $user->email }}<br>
    Senha temporária: <strong>{{ $password }}</strong>
</p>

<p>Por favor, faça login e altere a senha assim que possível.</p>
