@extends('layouts.app')

@section('content')

<div class="container mt-5">
    <h2>Home</h2>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if (session('warning'))
        <div class="alert alert-warning">
            {{ session('warning') }}
        </div>
    @endif

    @if (Auth::user()->role_id === 1)
        <a href="{{ route('guild.create') }}" class="btn btn-success mb-3">Criar Guilda</a>
        <form method="POST" action="{{ route('balance') }}">
            @csrf
            <button type="submit" class="btn btn-primary">Balancear Guildas</button>
        </form>      
    @endif

    <div class="row">
        <div class="col-md-6">
            <h3>Guildas</h3>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Número Mínimo de Jogadores</th>
                        <th>Número Máximo de Jogadores</th>
                        <th>Confirmado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($guids as $guild)
                        <tr>
                            <td>{{ $guild->id }}</td>
                            <td>{{ $guild->name }}</td>
                            <td>{{ $guild->min_players }}</td>
                            <td>{{ $guild->max_players }}</td>
                            <td>{{ $guild->confirmation_status }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if (Auth::user()->role_id === 1) 
            <div class="col-md-6">
                <h3>Players</h3>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Classe</th>
                            <th>Guilda</th>
                            <th>Confirmado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($players as $player)
                            <tr>
                                <td>{{ $player->id }}</td>
                                <td>{{ $player->name }}</td>
                                <td>{{ $player->class ? $player->class->name : 'Sem Classe' }}</td>
                                <td>{{ $player->guild ? $player->guild->name : 'Sem Guilda' }}</td>
                                <td>{{ $player->confirmed }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

@endsection
