@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Editar Guilda</h1>

    <form method="POST" action="{{ route('guild.update', $guild->id) }}">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="name">Nome da Guilda:</label>
            <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $guild->name) }}" required>
        </div>

        <div class="form-group">
            <label for="max_players">Maximo de players:</label>
            <input type="text" id="max_players" name="max_players" class="form-control" value="{{ old('max_players', $guild->max_players) }}" required>
        </div>

        <div class="form-group">
            <label for="min_players">Minimo de players:</label>
            <input type="text" id="min_players" name="min_players" class="form-control" value="{{ old('min_players', $guild->min_players) }}" required>
        </div>

        <div class="d-flex mt-3">
            <button type="submit" class="btn btn-primary me-2">Atualizar</button>
            <a href="{{ route('home') }}" class="btn btn-secondary">Voltar</a>
        </div>
    </form>
</div>
@endsection
