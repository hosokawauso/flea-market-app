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
  @livewireStyles
</head>

<body>
  <div class="app">
    <header class="header">
      <a href="/">
        <div class="logo">
          <img src="{{ asset('img/logo.svg') }}" alt="COACHTECH">
        </div>
      </a>

      @auth
      {{-- ログイン中だけ表示 --}}
      <form class="search-form" action="/search" method="get">
        @csrf
        <input class="search-form__keyword-input" type="text" name="keyword" placeholder="なにをお探しですか？" value="{{ request('keyword') }}">
      </form>
      
      <nav class="header__nav">
        <form action="/logout" method="post">
          @csrf
          <input class="header__link-logout" type="submit" value="ログアウト">
        </form>
        <a class="header__link-mypage" href="/mypage">マイページ</a>
        <a class="header__link-sell" href="/sell">出品</a>
      </nav>
      @endauth

      @guest
      <form class="search-form" action="/search" method="get">
        <input class="search-form__keyword-input" type="text" name="keyword" placeholder="なにをお探しですか？" value="{{ request('keyword') }}">
      </form>
      
      <nav class="header__nav">
        <a class="header__link-login" href="/login">ログイン</a>
        <a class="header__link-mypage" href="/mypage">マイページ</a>
        <a class="header__link-sell" href="/sell">出品</a>
      </nav>
     @endguest
    </header>

    <div class="content">
      @yield('content')

      
    </div>
  </div>
  @stack('scripts')
  @livewireScripts
</body>

</html>