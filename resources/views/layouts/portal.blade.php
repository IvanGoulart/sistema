<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'Portal do Cliente')</title>

  {{-- Bootstrap --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  @livewireStyles
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container">
    <a class="navbar-brand" href="{{ route('portal.home') }}">Portal</a>

    <div class="ms-auto d-flex gap-2">
      @auth
        <a class="btn btn-outline-light btn-sm" href="{{ route('portal.agendar') }}">Agendar</a>
        <form method="POST" action="{{ route('portal.logout') }}">
          @csrf
          <button class="btn btn-light btn-sm" type="submit">Sair</button>
        </form>
      @endauth
    </div>
  </div>
</nav>

<main class="py-4">
  @yield('content')
</main>

@livewireScripts
</body>
</html>
