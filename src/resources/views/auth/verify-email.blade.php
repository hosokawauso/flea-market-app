@extends('layouts.auth')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/verify-email.css') }}">
@endsection

@section('content')
<div class="verify-email__container">
  <div class="verify-email__header">
    <div class="verify-email__guid">
      <p>登録していただいたメールアドレスに承認メールを送付しました。</p>
      <p>メール承認を完了してください。</p>
    </div>
    <div class="verify-link">
      <button class="verify-button" disabled>認証はこちらから</button>
    </div>
    <div class="resend-verify-email">
      <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="resend-link">認証メールを再送する</button>
      </form>
    </div>
  </div>
</div>
@endsection