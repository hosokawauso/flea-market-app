@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
@endsection

@section('content')
<div class="mypage">
  <div class="mypage__heading">
    <div class="profile-img__info">
      @if (!empty($user->profile_img))
        <label for="profile_img">
        <img id="preview" src="{{ asset('storage/' . $user->profile_img) }}" alt="プロフィール画像">
        </label>
      @else
        <label for="profile_img">
        <div class="profile-img__placeholder">未設定</div>
        </label>
      @endif
        <div class="user-name">
        {{ $user->name }}
          @php
            $displayRating = $avgRatingInt ?? 0;
          @endphp

        <div class="rating-stars" aria-label="評価">
          @for($i = 1; $i <= 5; $i++)
            <span class="star {{ $i <= $displayRating ? 'is-on' : 'is-off' }}">★</span>
          @endfor
        </div>

        </div>
        <a class="profile-edit-button" href="/mypage/profile" >プロフィールを編集</a>
    </div>
  </div>

  <div class="mypage__inner">
    <div class="mypage__tabs">
      <a class="{{ $page === 'sell' ? 'active' : '' }}" href="/mypage?page=sell">出品した商品</a>
      <a class="{{ $page === 'buy' ? 'active' : '' }}" href="/mypage?page=buy">購入した商品</a>
      <a class="{{ $page === 'transaction' ? 'active' : '' }}" href="/mypage?page=transaction">取引中の商品</a>

    </div>

    <div class="mypage__items">
      @if ($page === 'sell')
        @forelse ($sellingItems as $item)
          <div class="item-card">
            <a href="/item/{{ $item->id }}" class="item-card">
            <img src="{{ asset('storage/' .$item->item_img) }}" alt="{{ $item->item_name }}">
            <p> {{ $item->item_name }}</p>
            </a>
          </div>
        @empty
          <p class="empty-message">出品した商品はありません。</p>
        @endforelse

      @elseif($page === 'buy')
        @forelse($purchasedItems as $item)
          <div class="item-card">
            <a href="/item/{{ $item->id }}" class="item-card">
            <img src="{{ asset('storage/' .$item->item_img) }}" alt="{{ $item->item_name }}">
            <p>{{ $item->item_name }}</p>
            </a>
          </div>
          @empty
            <p class="empty-message">購入した商品はありません。</p>
          @endforelse

        @elseif($page === 'transaction')
          @forelse($transactions as $transaction)
            <div class="item-card">
              @if(($transaction->unread_count ?? 0) > 0)
                <span class="badge">{{ $transaction->unread_count }}</span>
              @endif
              <a href="{{ route('transactions.show', ['transaction' => $transaction->id]) }}" class="item-card">
              <img src="{{ asset('storage/' . $transaction->purchase->item->item_img) }}" alt="{{ $transaction->purchase->item->item_name }}">
              <p>{{ $transaction->purchase->item->item_name }}</p>
              </a>
            </div>
          @empty
            <p class="empty-message">取引中の商品はありません。</p>
        @endforelse
      @endif
    </div>
  </div>
</div>
@endsection