<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container-fluid d-flex justify-content-between align-items-center">
    <div class="d-none d-lg-block" style="width: 100px;"></div> 

    <h1 class="m-0 text-center flex-grow-1">
      <strong>Magico de Yetz</strong>
    </h1>

    <div class="d-flex">
      <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="btn btn-danger">Logout</a>
      <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
          @csrf
      </form>
    </div>
  </div>
</nav>
