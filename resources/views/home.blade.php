@extends('layouts.app')

@section('content')

<div class="container mt-5">
    @if (Auth::user()->role_id === 1)
        <div class="d-flex gap-2 buttons__guilda">
            <a href="{{ route('guild.create') }}">
                <button type="button" class="btn btn-success w-100">Criar Guilda</button>
            </a>
            <form method="POST" action="{{ route('balance') }}">
                @csrf
                <button type="submit" class="btn btn-primary w-100">Balancear Guildas</button>
            </form>
        </div>
    @endif

    <div class="row mt-5">
        <div class="col">
            <h3>Guildas</h3>
            <table class="table table-striped">
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
                                <a href="{{ route('guild.show', $guild->id) }}" class="btn btn-sm btn-info">Visualizar</a>
                                @if (Auth::user()->role_id === 1) 
                                    <a href="{{ route('guild.edit', $guild->id) }}" class="btn btn-sm btn-warning">Editar</a>
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
    </div>

    <div class="row mt-5">
        <div class="col">
            <h3>Players</h3>
            <table class="table table-striped">
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
