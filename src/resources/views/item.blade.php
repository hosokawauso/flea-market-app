@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/item.css') }}">
@endsection

@section('content')
<div class="product-detail">

  <div class="product-image-area">
    <div class="product-image">
      @if (Str::startsWith($item->item_img, 'http'))
      <img src="{{ $item->item_img }}" alt="{{ $item->item_name }}">
      @else
      <img src="{{ asset('storage/' .$item->item_img) }}" alt="{{ $item->item_name }}">
      @endif
    </div>
  </div>


  <div class="product-description-area">
    <div class="product-title">
      {{ $item->item_name }}
    </div>

    <div class="brand_name">
      {{ $item->brand_name }}
    </div>

    <div class="price-area">
      <span>¥</span>
      <span class="price">{{ number_format($item->price) }}</span>
      <span>(税込)</span>
    </div>

    <div class="product-actions">
      <div class="favorite-counter">
        <form class="inline" action="{{ route('item.favorite', $item) }}" method="post">
          @csrf
        <button class="favorite-button">
          @if (Auth::check() && Auth::user()->favoriteItems->contains($item))
          <img src="{{ asset('img/selected.jpeg') }}" alt="いいね解除" >
          @else
          <img src="{{ asset('img/star.jpeg') }}" alt="いいね" >
          @endif
        </button>
      </form>
        <div class="favorite-count">
          {{ $item->favoritedBy()->count() }}
        </div>
      </div>


      <div class="comments-counter">

        <img src="{{ asset('img/bubble.jpeg') }}" alt="comment" >

        <div class="comment-count">
          {{ $item->comments->count() }}
        </div>
      </div>

    </div>

{{-- 購入ボタン：購入確認画面へ遷移 --}}
      <div class="purchase-area">
{{--         <form  class="purchase-box" action="{{ route('item.purchase', ['item' => $item->id]) }}" method="get">
--}}           <div class="purchase-box__button">
            {{-- <input class="purchase-box__button-submit" type="submit" value="購入手続きへ"> --}}
          @if (!$item->purchase)
            <a href="{{ route('item.purchase', ['item' => $item->id]) }}" class="purchase-procedure">購入する</a>
          @else
            <p class="sold-out" >Sold</p>
          @endif

          </div>
         {{-- </form> --}}
      </div>

      <div class="product-description">

      <h3>商品説明</h3>
      {{ $item->description }}
    </div>

    <div class="product-info">
      <h3>商品の情報</h3>

        <div class="category-info">
          <div class="info-label">カテゴリー</div>
        <ul class="category-list">
        @foreach ($item->categories as $category)
          <li>{{ $category->content }}</li>
        @endforeach
        </ul>
      </div>


        <div class="product-condition">
          <div class="info-label">商品の状態</div>
            <div class="condition">
              @switch($item->condition)
              @case(1)
                  良好
                  @break
              @case(2)
                  目立った傷や汚れなし
                  @break
              @case(3)
                  やや傷や汚れあり
                  @break
              @case(4)
                  状態が悪い
                  @break
              @endswitch       
            </div>
        </div>

      </div>

    <div class="product-comments">
      <h3>コメント({{ $comments->count() }})</h3>
    </div>

    <ul class="comment-list">
    
    @foreach ($comments as $comment)

    <li class="comment-text">

      <div class="comment-header">
        @if (isset($user) && $user->profile_img)
          <img id="preview" src="{{ asset('storage/' . $comment->user->profile_img) }}" alt="プロフィール画像">
        @else
          <div class="profile-img__placeholder">未設定</div>
        @endif
        <span class="comment-user">{{ $comment->user->name }}</span>
      </div>
      <p class="comment-body">{{ $comment->body }}</p>
    </li>
    @endforeach
  </ul>


      <div class="comment-text"></div>

    <div class="comment-input">
      <form  class="comments-form" action="{{ route('item.comment.store', ['item' => $item->id]) }}" method="post">
        @csrf
        <label class="comments-form__label" for="comment" >商品へのコメント</label>
        <textarea class="comments-form__textarea"  name="body" id="comment" required>{{ old('body') }} </textarea>
        <p class="register-form__error-message">
          @error('body')
          {{ $message }}
          @enderror
        </p>


          <div class="comments-form__button">
            <button class="comments-form__button-submit" type="submit">コメントを送信する</button>
          </div>
      </form>
    </div>
</div>
</div>


</div>
@endsection