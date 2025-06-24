@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/item-detail/css') }}">
@endsection

@section('content')
<div class="detail">
  <div class="detail__inner">
    <div class="item-img">
      <img src="{{ asset('storage/' .$item->item_img) }}" alt="{{ $item->item_name }}">
    </div>

    <div縁 class="item-introduce">
      <div class="item-name">
        {{-- {{ $item->item_name }} --}} 商品名がここでーす
      </div>
      <div class="brand">ブランド名</div>
      <div class="price">金額</div>
    </div縁\

  </div>
</div>
@endsection