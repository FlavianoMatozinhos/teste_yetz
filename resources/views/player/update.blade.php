@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-6">
            <h3>Editar Perfil do Jogador</h3>
            <form action="{{ route('player.update', $player->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="name">Nome</label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $player->name) }}" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $player->email) }}" required>
                </div>

                <div class="form-group">
                    <label for="password">Senha</label>
                    <input type="password" name="password" id="password" class="form-control" value="{{ old('password', $player->password) }}" required>
                </div>

                <div class="form-group">
                    <label for="class_id">Classe</label>
                    <select name="class_id" id="class_id" class="form-control" required>
                        <option value="">Selecione a Classe</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ $class->id == $player->class_id ? 'selected' : '' }}>{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="xp">Sua Experiencia</label>
                    <input type="text" name="xp" id="xp" class="form-control" value="{{ old('xp', $player->xp) }}" required>
                </div>

                <div class="row mt-3">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-success">Salvar Alterações</button>
                        <a href="{{ route('player.show', $player->id) }}" class="btn btn-primary">Voltar</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
