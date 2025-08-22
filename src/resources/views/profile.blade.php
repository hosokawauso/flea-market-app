@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
<div class="profile">
  <h2 class="profile__title">プロフィール設定</h2>
  <div class="profile__inner">
    <form class="profile-form__form" action="/mypage/profile" method="post" enctype="multipart/form-data">
      @csrf
      <div class="profile-img">
        <label for="profile_img">
        <img 
          id="preview"
          src="{{ !empty($user->profile_img) ? asset('storage/' .$user->profile_img) : asset('img/default.png') }}"  
          alt="プロフィール画像"
          class="{{ empty($user->profile_img) ? 'profile-img__placeholder' : '' }}">
        </label>
          
        {{-- @if (!empty($user->profile_img))
          <label for="profile_img">
            <img id="preview" src="{{ asset('storage/' . $user->profile_img) }}" alt="プロフィール画像">
          </label>
        @else
          <label for="profile_img">
            <img class="profile-img__placeholder" id="preview" src="{{ asset('img/default.png')}}" alt="未設定プロフィール画像">
          </label>
        @endif --}}
          <input type="file" id="profile_img" name="profile_img" accept="image/*" hidden>
          <label class="upload-button" for="profile_img">画像を選択する</label>
      </div>
      <div class="profile-form__group">
        <label class="profile-form__label" for="name">ユーザー名</label>
        <input class="profile-form__input" type="text" name="name" id="name" value="{{ old('name', $user->name) }}">
        <p class="profile-form__error-message">
          @error('name')
          {{ $message }}
          @enderror
        </p>
      </div>
      <div class="profile-form__group">
        <label class="profile-form__label" for="postal_code">郵便番号</label>
        <input class="profile-form__input" type="text" name="postal_code" id="postal_code" inputmode="numeric"  value="{{ old('postal_code', $user->postal_code) }}">
        <p class="profile-form__error-message">
          @error('postal_code')
          {{ $message }}
          @enderror
        </p>
      </div>
      <div class="profile-form__group">
        <label class="profile-form__label" for="address">住所</label>
        <input class="profile-form__input" type="text" name="address" id="address" value="{{ old('address', $user->address) }}">
        <p class="profile-form__error-message">
          @error('address')
          {{ $message }}
          @enderror
        </p>
      </div>
      <div class="profile-form__group">
        <label class="profile-form__label" for="building">建物名</label>
        <input class="profile-form__input" type="text" name="building" id="building" value="{{ old('building', $user->building) }}">
      </div>
      <div class="profile-form__button">
          <button class="profile-form__button-submit" type="submit">更新する</button>
      </div> 
    </form>
  </div>
</div>
@endsection

@push('script')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const input   = document.getElementById('profile_img');
  const preview = document.getElementById('preview');
  const button  = document.querySelector('.upload-button');

  input.addEventListener('change', event => {
    const file = event.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = (readEvent) => {
      if (preview) {
        preview.src = readEvent.target.result;
        preview.hidden = false;
      } else {
        const img = document.createElement('img');
        img.id = 'preview';
        img.src = readEvent.target.result;
        img.alt = 'プロフィール画像';
        document.querySelector('.profile-img label').innerHTML = '';
        document.querySelector('.profile-img label').appendChild(img);
      }
    };
    reader.readAsDataURL(file);
  });
});
</script>
@endpush
