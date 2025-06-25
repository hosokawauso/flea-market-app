@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
<div class="profile">
  <h2 class="profile__title">プロフィール設定</h2>
    <div class="profile__inner">
      <form class="profile-form__form" action="#" method="post" enctype="multipart/form-data">
        @csrf
        @method('PATCH')
        <div class="profile-img">
          @if (isset($user) && $user->profile_img)
      <label for="profile_img">
        <img id="preview" src="{{ asset('storage/' . $user->profile_img) }}" alt="プロフィール画像">
      </label>
          @else
            <label for="profile_img">
              <div class="profile-img__placeholder">未設定</div>
            </label>
          @endif

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

  </div>
</div>
@endsection