@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase-address.css') }}">
@endsection

@section('content')
<div class="purchase-address">
  <h2 class="purchase-address__title">住所の変更</h2>
  <div class="purchase-address__inner">

    <form class="purchase-address-form__form" action="{{ route('purchase.address.update',['item' => $item->id]) }}" method="post" >
      @csrf
      <div class="purchase-address-form__group">
        <label class="purchase-address-form__label" for="postal_code">郵便番号</label>
        <input class="purchase-address-form__input" type="text" name="postal_code" id="postal_code" inputmode="numeric" value="{{ old('postal_code') }}">
        <p class="purchase-address-form__error-message">
          @error('postal_code')
          {{ $message }}
          @enderror
        </p>
      </div>

      <div class="purchase-address-form__group">
        <label class="purchase-address-form__label" for="address">住所</label>
        <input class="purchase-address-form__input" type="text" name="address" id="address" value="{{ old('address') }}">
        <p class="purchase-address-form__error-message">
          @error('address')
          {{ $message }}
          @enderror
        </p>
      </div>
      
      <div class="purchase-address-form__group">
        <label class="purchase-address-form__label" for="building">建物名</label>
        <input class="purchase-address-form__input" type="text" name="building" id="building" value="{{ old('building') }}">
      </div>
      <div class="purchase-address-form__button">
          <button class="purchase-address-form__button-submit" type="submit">更新する</button>
      </div>
    </form>
  </div>
</div>

@endsection



