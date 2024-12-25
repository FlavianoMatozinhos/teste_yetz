@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-6">
            <h3>Perfil do Jogador</h3>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Classe</th>
                        <th>Guilda</th>
                        <th>Confirmado</th>
                        <th>Açao</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $player->name }}</td>
                        <td>{{ $player->class ? $player->class->name : 'Sem Classe' }}</td>
                        <td>{{ $player->guild ? $player->guild->name : 'Sem Guilda' }}</td>
                        <td>{{ $player->confirmed ? 'Sim' : 'Não' }}</td>
                        <td>
                            <a href="{{ route('player.edit', $player->id) }}" class="btn btn-success">Editar Jogador</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-12">
            <a href="{{ route('home') }}" class="btn btn-primary">Voltar</a>
        </div>
    </div>
</div>
@endsection