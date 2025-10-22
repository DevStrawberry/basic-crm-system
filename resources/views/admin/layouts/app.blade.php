<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Gestão de Usuários')</title>
</head>
<body>
<nav>
    <a href="{{ route('admin.users.index') }}">Usuários</a>
</nav>
<hr>
<main>
    @yield('content')
</main>
</body>
</html>
