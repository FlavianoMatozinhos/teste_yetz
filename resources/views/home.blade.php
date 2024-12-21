@extends('layouts.app')

@section('content')

<div class="container mt-5">
    <h2>Home</h2>

    <!-- Alerta de sucesso -->
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Alerta de erro -->
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <!-- Alerta de aviso (Warning) -->
    @if (session('warning'))
        <div class="alert alert-warning">
            {{ session('warning') }}
        </div>
    @endif

    @if (Auth::user()->role_id === 1)  <!-- Verifica se o usuário é o mestre -->
        <a href="{{ route('guild.create') }}" class="btn btn-success mb-3">Criar Guilda</a>
        <form method="POST" action="{{ route('balance') }}">
            @csrf
            <label for="num_guildas">Número de Guildas:</label>
            <select name="num_guildas" id="num_guildas">
                @for ($i = 1; $i <= $numGuildas; $i++)
                    <option value="{{ $i }}">{{ $i }} Guilda{{ $i > 1 ? 's' : '' }}</option>
                @endfor
            </select>
            
            <button type="submit">Balancear</button>
        </form>      
    @endif

    <!-- Layout com as tabelas lado a lado -->
    <div class="row">
        <!-- Tabela de Guildas -->
        <div class="col-md-6">
            <h3>Guildas</h3>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Número Mínimo de Jogadores</th>
                        <th>Número Máximo de Jogadores</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($guids as $guild)
                        <tr>
                            <td>{{ $guild->id }}</td>
                            <td>{{ $guild->name }}</td>
                            <td>{{ $guild->min_players }}</td>
                            <td>{{ $guild->max_players }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Tabela de Players -->
        <div class="col-md-6">
            <h3>Players</h3>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Classe</th>
                        <th>Guilda</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($players as $player)
                        <tr>
                            <td>{{ $player->id }}</td>
                            <td>{{ $player->name }}</td>
                            <td>{{ $player->class ? $player->class->name : 'Sem Classe' }}</td>
                            <td>{{ $player->guild ? $player->guild->name : 'Sem Guilda' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
