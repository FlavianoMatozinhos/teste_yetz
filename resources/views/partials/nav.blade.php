<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
      <!-- Logo centralizada -->
      {{-- <a class="navbar-brand mx-auto" href="{{ url('/') }}">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" style="height: 40px;">
      </a> --}}
  
      <!-- Botão de "Ranking" -->
      <div class="navbar-nav me-auto mb-2 mb-lg-0">
        {{-- <a class="nav-link" href="{{ route('ranking.index') }}">Ranking</a> --}}
      </div>
  
      <!-- Botão de Logout à direita -->
      <div class="d-flex">
        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="btn btn-danger">Logout</a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
      </div>
    </div>
  </nav>
  