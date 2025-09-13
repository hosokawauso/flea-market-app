@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection

@section('content')
<div class="purchase-screen">
  <div class="purchase-info">
    <div class="purchase-info__inner">
      <div class="item-image">
        <img src="{{ asset('storage/' .$item->item_img) }}" alt="{{ $item->item_name }}">
      </div>
      <div class="item-info">
        <div class="item-title">
          {{ $item->item_name }}
        </div>
        <div class="price-area">
          <span class="yen">¥</span>
          <span class="price">{{ number_format($item->price) }}</span>
        </div>
      </div>
    </div>

    <form action="{{ route('payment.checkout', ['item' => $item->id]) }}" method="POST">
    @csrf
    <div class="payment-method-area">
      <p class="payment-method-area__title">支払い方法</p>
      <select id="payment-select" class="payment-select" name="payment_method">
        <option value="" disabled {{ old('payment_method' ) ? '' : 'selected'}}>選択してください</option>
          @foreach(config('payments.methods') as $value => $label)
            <option value="{{ $value }}" {{ old('payment_method')===$value ? 'selected' : ''}}>
            {{ $label }}
            </option>
          @endforeach
      </select>
      <p class="payment-method__error-message">
        @error('payment_method')
        {{ $message }}
        @enderror
        @error('payment')
        <p class="error-message">{{ $message }}</p>
        @enderror
      </p>
    </div>
    <div class="purchase-address-area">
      <div class="purchase-address-area__header">
        <div class="purchase-address-area__title">配送先</div>
        <a class="purchase-address-area__btn" href="{{ route('purchase.address.edit',['item' => $item->id] )}}">変更する</a>
      </div>
    @if ($changeAddress)
      @include('purchase-address', ['purchase' => $purchase])
    @else
      <div class="purchase-address" id="address-display">
        <div class="postal-code">
        <span>〒</span>
        {{ $purchase['postal_code']}}
        </div>
        <div class="address">
        {{ $purchase['address']}}{{ $purchase['building'] }}
        </div>
      </div>
      @error('purchase_address')
        <p class="purchase-address__error-message">{!! nl2br(e($message)) !!}</p>
      @enderror
      <div id="address-form" style="display: none;">
        <input type="hidden" name="postal_code" value="{{ $purchase['postal_code'] }}">
        <input type="hidden" name="address" value="{{ $purchase['address'] }}">
        <input type="hidden" name="building" value="{{ $purchase['building'] }}">
      </div>
      @endif
    </div>
  </div>
  <div class="confirm-surface">
    <table class="summary__table">
      <tr>
        <th>商品代金</th>
        <td>
          <span class="yen">¥</span>
          <span class="price">{{ number_format($item->price) }}</span>
        </td>
      </tr>
      <tr>
        <th>支払い方法</th>
        <td id="selected-payment-method">
          未選択
        </td>
      </tr>
    </table>

      <button class="purchase-confirm__button-submit"  type="submit">
          購入する
      </button>
    </form>
  </div>
</div>

@endsection

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const select = document.getElementById('payment-select');
    const display = document.getElementById('selected-payment-method');

    select.addEventListener('change', function () {

      const selectedText = select.options[select.selectedIndex].text;

      display.textContent = selectedText;
    });
  });
</script>