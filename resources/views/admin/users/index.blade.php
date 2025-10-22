@extends('layouts.app')

@section('content')
    <h1>Lista de Usuários</h1>

    <a href="{{ route('users.create') }}">Novo Usuário</a>

    @if(session('success'))
        <p style="color: green">{{ session('success') }}</p>
    @endif

    <table border="1" cellpadding="8">
        <tr>
            <th>ID</th><th>Nome</th><th>Email</th><th>Ações</th>
        </tr>
        @foreach($users as $user)
            <tr>
                <td>{{ $user->id }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    <a href="{{ route('users.show', $user) }}">Ver</a> |
                    <a href="{{ route('users.edit', $user) }}">Editar</a> |
                    <form action="{{ route('users.destroy', $user) }}" method="POST" style="display:inline">
                        @csrf @method('DELETE')
                        <button type="submit" onclick="return confirm('Tem certeza?')">Excluir</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </table>
@endsection
