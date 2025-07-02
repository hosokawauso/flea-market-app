@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sell.css') }}">
@endsection

@section('content')
<div class="sell-form">
  <h2 class="sell-form__heading">商品の出品</h2>

  <div class="sell-form__inner">
    <form class="sell-form__form" action="/sell" method="post" enctype="multipart/form-data">
      @csrf      
        <div class="sell-form__img-upload">
          <div class="label-wrapper">
            <label class="sell-form__label" for="item_img">商品画像</label>
            <p class="sell-form__error-message">
              @error('item_img')
              {{ $message }}
              @enderror
            </p>
          </div>

          <div class="img-upload-box">
            <label class="img-upload-button" for="item_img">画像を選択する</label>

            <img class="img-preview"  id="preview" src="{{ asset('images/placeholder.png') }}" alt="商品画像" hidden>

            <input class="sell-form__input" type="file" name="item_img" id="item_img" accept="image/*" style="display: none;" >


          </div>
        </div>


      <h3>商品の詳細</h3>

      <div class="sell-form__category">
        <div class="label-wrapper">
        <label class="sell-form__label">カテゴリー</label>
        <p class="sell-form__error-message">
          @error('category')
          {{ $message }}
          @enderror
        </p>
        </div>

        <div class="category-list">
          @foreach ($categories as $category)
            <label class="sell-form__category-button">
              <input class="sell-form__input-category" type="checkbox" name="category[]" value="{{ $category->id }}" hidden {{ in_array($category->id, old('category', [])) ? 'checked' : '' }}>
              <span class="category-content">{{ $category->content }}</span>      
            </label>
          @endforeach
        </div>
      </div>

      <div class="sell-form__condition">
        <div class="label-wrapper">
          <label class="sell-form__label">商品の状態</label>
          <p class="sell-form__error-message">
            @error('condition')
            {{ $message }}
            @enderror
          </p>
        </div>
        <select class="sell-form__condition-select"  name="condition" value="{{ old('condition') }}">
          <option disabled selected>選択してください</option>
          <option value="1" @if( old('condition')==1 ) selected @endif>良好</option>
          <option value="2" @if( old('condition')==2 ) selected @endif>目立った傷や汚れなし</option>
          <option value="3" @if( old('condition')==3 ) selected @endif>やや傷や汚れあり</option>
          <option value="4" @if( old('condition')==4 ) selected @endif>状態が悪い</option>
        </select>
      </div>

      <h3>商品名と説明</h3>
      <div class="sell-form__item-name">
        <div class="label-wrapper">
          <label class="sell-form__label" for="item_name">商品名</label>
          <p class="sell-form__error-message">
            @error('item_name')
            {{ $message }}
            @enderror
          </p>
        </div>
        <input class="sell-form__input" type="text" name="item_name" id="item_name" value="{{ old('item_name') }}">
      </div>
      <div class="sell-form__brand-name">
        <label class="sell-form__label" for="brand_name">ブランド名</label>
        <input class="sell-form__input" type="text" name="brand_name" id="brand_name" value="{{ old('brand_name') }}">
      </div>

      <div class="sell-form__description">
        <div class="label-wrapper">
          <label class="sell-form__label" for="description">商品の説明</label>
          <p class="sell-form__error-message">
            @error('description')
            {{ $message }}
            @enderror
          </p>
        </div>
        <textarea class="sell-form__input" name="description" id="description" cols="30" rows="10">{{ old('description') }}</textarea>
      </div>

      <div class="sell-form__price">
        <div class="label-wrapper">
          <label class="sell-form__label" for="price">販売価格</label>
          <p class="sell-form__error-message">
            @error('price')
            {{ $message }}
            @enderror
          </p>
        </div>
        <span class="input-prefix">¥</span>
        <input class="sell-form__input price-mark" type="number" name="price" id="price" value="{{ old('price') }}">
      </div>

      <button class="sell-form__button-submit" type="submit">出品する</button>
    </form>
  </div>
</div>
@endsection

@push('scripts')  {{-- layouts/app.blade.php に @stack('scripts') が必要 --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
  const input   = document.getElementById('item_img');
  const preview = document.getElementById('preview');
  const button  = document.querySelector('.img-upload-button');

  input.addEventListener('change', e => {
    const file = e.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = ev => {
      preview.src = ev.target.result;
      preview.hidden = false;
      button.style.display = 'none';
    };
    reader.readAsDataURL(file);
  });
});
</script>
@endpush