@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection

@section('content')
{{-- <div class="purchase-screen">
 --}}
{{--   <div class="purchase-info">

    <div class="purchase-info__inner">
      <div class="product-image">
       <img src="{{ asset('storage/' .$item->item_img) }}" alt="{{ $item->item_name }}">
       <img src="https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Armani+Mens+Clock.jpg" alt="image">
      </div>
      <div class="product-info">
        <div class="product-title">
          {{ $item->item_name }}
        </div>
        <div class="price-area">
          <span class="yen">¥</span>
          <span class="price">{{ number_format($item->price) }}</span>
        </div>
      </div>
    </div>

    <div class="payment-method-area">
      <div class="payment-method-area__title">支払い方法</div>
      <select class="payment-select" name="payment-select" id="payment-select" >
      </select>
    </div>

    <div class="purchase-address-area">
      <div class="purchase-address-area__header">
        <div class="purchase-address-area__title">配送先</div>
      <form class="purchase-address-update" action="{{ route('purchase.address.update', ['item'=> $item->id]) }}" method="get">
        @csrf
        <div class="purchase-box__button">
          <input class="purchase-box__button-submit" type="submit" value="変更する">
        </div>
      </div>
      <div class="purchase-address">
        <div class="postal-code">
          <span>〒</span> 
          {{ $user->postal_code }}
        </div>
          <div class="address">
            {{ $user->address }}{{ $user->building }}
          </div>
        </div>

      </form>
    </div>
  </div>

  
  <div class="confirm-surface">
    <form  class="purchase-confirm" action="" method="post">
    <div class="product-price">商品代金</div>
    <span>¥</span>
    <span class="price">{{ number_format($item->price) }}</span>

    <div class="payment">支払い方法</div>
    <span class="method"></span>
    
      @csrf
      <input type="number" class="price" value="商品代金">

    </form>
  

  <table class="summary">
    <tr>
      <th>商品代金</th>
      <td><span class="yen">¥</span>
          <span class="price">{{ number_format($item->price) }}</span>
      </td>
    </tr>
    <tr>
      <th>支払い方法</th>
      <td></td>
    </tr>
  </table>
</div>
 --}}

{{-- </div> --}}
<livewire:payment-method :item="$item" :user="Auth::user()"/>
{{-- @livewire('payment-method') --}}
@endsection