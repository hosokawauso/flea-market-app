<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>COACHTECH フリマアプリ</title>
    <link rel="stylesheet" href="https://unpkg.com/ress/dist/ress.min.css">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    @yield('css')
  </head>

  <body>
    <div class="app">
      <header class="header">
        <a href="/">
          <div class="logo">
            <img src="{{ asset('img/logo.svg') }}" alt="COACHTECH">
          </div>
        </a>
      </header>

      <div class="content">
        @yield('content')
      </div>
    </div>
    @stack('scripts')
  </body>
</html>