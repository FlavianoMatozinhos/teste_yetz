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
                        <th>Nome</th>
                        <th>Número Mínimo de Jogadores</th>
                        <th>Número Máximo de Jogadores</th>
                        <th>Confirmado</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($guilds as $guild)
                        <tr>
                            <td>{{ $guild->name }}</td>
                            <td>{{ $guild->min_players }}</td>
                            <td>{{ $guild->max_players }}</td>
                            <td>{{ $guild->confirmation_status }}</td>
                            <td>
                                <!-- Botão para Visualizar -->
                                <a href="{{ route('guild.show', $guild->id) }}" class="btn btn-sm btn-info">Visualizar</a>
                                @if (Auth::user()->role_id === 1) 
                                    <!-- Botão para Editar -->
                                    <a href="{{ route('guild.update', $guild->id) }}" class="btn btn-sm btn-warning">Editar</a>
                                    
                                    <!-- Formulário para Excluir -->
                                    <form action="{{ route('guild.destroy', $guild->id) }}" method="POST" style="display: inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir esta guilda?')">Excluir</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="col-md-6">
            <h3>Players</h3>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Classe</th>
                        <th>Guilda</th>
                        <th>Confirmado</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($players as $player)
                        @if (Auth::user()->role_id === 2 && Auth::user()->id !== $player->id)
                            @continue
                        @endif
                        <tr>
                            <td>{{ $player->name }}</td>
                            <td>{{ $player->class ? $player->class->name : 'Sem Classe' }}</td>
                            <td>{{ $player->guild ? $player->guild->name : 'Sem Guilda' }}</td>
                            <td>{{ $player->confirmed }}</td>
                            <td>
                                @if (Auth::user()->confirmed === 0)
                                    <a href="{{ route('player.confirm', $player->id) }}" class="btn btn-sm btn-success">Confirmar</a>
                                @else
                                    <a href="{{ route('player.noconfirm', $player->id) }}" class="btn btn-sm btn-warning">Não confirmar</a>
                                @endif

                                <a href="{{ route('player.show', $player->id) }}" class="btn btn-sm btn-info">Visualizar</a>
                                
                                @if (Auth::user()->role_id === 1)        
                                    <form action="{{ route('player.destroy', $player->id) }}" method="POST" style="display: inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir esta guilda?')">Excluir</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>        
    </div>
</div>

@endsection
