@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Criar Guilda</h2>

    <!-- Exibição de mensagens de sucesso ou erro -->
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @elseif(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if (Auth::user()->role_id !== 1)
        <div class="alert alert-danger">
            Você não tem permissão para criar uma guilda.
        </div>
    @else
        <form action="{{ route('guild.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">Nome da Guilda</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>

            <div class="mb-3">
                <label for="min_players" class="form-label">Número Mínimo de Jogadores</label>
                <input type="number" class="form-control" id="min_players" name="min_players" required>
            </div>

            <div class="mb-3">
                <label for="max_players" class="form-label">Número Máximo de Jogadores</label>
                <input type="number" class="form-control" id="max_players" name="max_players" required>
            </div>

            <button type="submit" class="btn btn-primary">Criar Guilda</button>
        </form>
    @endif
</div>
@endsection
