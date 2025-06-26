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
      <div class="sell-form__img">
        <div class="sell-form__img-upload">
          <label class="sell-form__label" for="item_img">商品画像</label>
          <div class="img-upload-box">
            <label class="img-upload-button" for="item_img">画像を選択する
            <input class="sell-form__input" type="text" name="item_img" id="item_img" hidden>
            </label>
          </div>
        </div>
      </div>

      <h3>商品の詳細</h3>

      <div class="sell-form__category">
        <h4>カテゴリー</h4>
        <div class="category-list">
          @foreach ($categories as $category)
            <label class="sell-form__category-button">
              <input class="sell-form__input-category" type="checkbox" name="category[]" id="" value="{{ $category->id }}" hidden>
              <span class="category-content">{{ $category->content }}</span>
            </label>
          @endforeach
        </div>
      </div>

      <div class="sell-form__condition">
        <h4>商品の状態</h4>
        <select class="sell-form__condition-select"  name="condition" value="{{ request('condition') }}">
          <option disabled selected>選択してください</option>
          <option value="1" @if( request('condition')==1 ) selected @endif>良好</option>
          <option value="2" @if( request('condition')==2 ) selected @endif>目立った傷や汚れなし</option>
          <option value="3" @if( request('condition')==3 ) selected @endif>やや傷や汚れあり</option>
          <option value="4" @if( request('condition')==4 ) selected @endif>状態が悪い</option>
        </select>
      </div>

      <h3>商品名と説明</h3>
      <div class="sell-form__item-name">
        <label class="sell-form__label" for="item_name">商品名</label>
        <input class="sell-form__input" type="text" name="item_name" id="item_name" value="{{ old('item_name') }}">
      </div>
      <div class="sell-form__brand-name">
        <label class="sell-form__label" for="brand_name">ブランド名</label>
        <input class="sell-form__input" type="text" name="brand_name" id="brand_name" value="{{ old('brand_name') }}">
      </div>
      <div class="sell-form__description">
        <label class="sell-form__label" for="description">商品の説明</label>
        <textarea class="sell-form__input" name="description" id="description" cols="30" rows="10">{{ old('description') }}</textarea>
      </div>
      <div class="sell-form__price">
        <label class="sell-form__label" for="price">販売価格</label>
        <input class="sell-form__input" type="number" name="price" id="price" value="{{ old('price') }}">
      </div>

      <button class="sell-form__button-submit" type="submit">出品する</button>










    </form>
  </div>
</div>
@endsection