@extends('layouts.auth')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/login.css') }}">
@endsection

@section('content')
<div class="login-form">
  <h2 class="login-form__heading">
    ログイン
  </h2>
  <div class="login-form__inner">
    <form class="login-form__form" action="/login" method="post">
      @csrf
      <div class="login-form__group">
        <label class="login-form__label" for="email">メールアドレス</label>
        <input class="login-form__input" type="text" inputmode="email" autocomplete="email" name="email" id="email" value="{{ old('email')}}">
        <p class="login-form__error-message">
        @error('email')
          {{ $message }}
        @enderror
        </p>
      </div>
      <div class="login-form__group">
        <label class="login-form__label" for="password">パスワード</label>
        <input class="login-form__input" type="password" name="password" id="password" value="{{ old('password')}}">
        <p class="login-form__error-message">
          @error('password')
            {{ $message }}
          @enderror
        </p>
      </div>

      <button class="login-form__button-submit" type="submit">ログインする</button>
    </form>
    <div class="register__link">
      <a class="register__button-submit" href="/register">会員登録はこちら</a>
    </div>
  </div>
</div>
@endsection

