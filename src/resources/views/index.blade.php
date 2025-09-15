@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
<div class="top__inner">
  <div class="top__tabs">
    <a class="{{ $page === 'recommend' ? 'active' : '' }}" href="/?keyword={{ request('keyword') }}">おすすめ</a>
    <a class="{{ $page === 'mylist' ? 'active' : '' }}" href="/?page=mylist&keyword={{ request('keyword') }}">マイリスト</a>
  </div>
  <div class="top__items">
    @if ($page === 'recommend')
      @foreach ($recommendItems as $item)
        <div class="item-card">
          <div class="item-img">
            <a href="/item/{{ $item->id }}" class="item-card">
            <img src="{{ asset('storage/' . rawurlencode($item->item_img)) }}" alt="{{ $item->item_name }}" class="{{ $item->purchase ? 'sold-image' : '' }}">
            </a>
            @if ($item->purchase)
            <span class="sold-label">Sold</span>
            @endif
          </div>
          <p>{{ $item->item_name }}</p>
        </div>
      @endforeach
    @elseif($page === 'mylist')
    @forelse ($favoriteItems as $item)
      <div class="item-card">
        <div class="item-img">
          <a href="/item/{{ $item->id }}" class="item-card">
          <img src="{{ asset('storage/' .$item->item_img) }}" alt="{{ $item->item_name }}" class="{{ $item->purchase ? 'sold-image' : '' }}">
          </a>
          @if ($item->purchase)
            <span class="sold-label">Sold</span>
          @endif
        </div>
        <p>{{ $item->item_name }}</p>
      </div>
    @empty
      <div class="empty-message">
      マイリストに登録した商品はありません
      </div>
    @endforelse
    @endif
  </div>
</div>
@endsection