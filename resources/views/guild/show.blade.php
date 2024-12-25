@extends('layouts.app')

@section('content')

<div class="container mt-5">
    <h2>Guilda: {{ $guild->name }}</h2>

    <div class="mb-3">
        <a href="{{ route('home') }}" class="btn btn-secondary">Voltar</a>
    </div>

    <h3>Jogadores</h3>

    @if($players->isEmpty())
        <p>Não há jogadores nesta guilda.</p>
    @else
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Classe</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($players as $player)
                    <tr>
                        <td>{{ $player->id }}</td>
                        <td>{{ $player->name }}</td>
                        <td>{{ $player->class ? $player->class->name : 'Sem Classe' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

@endsection
