<div class="purchase-screen">

<div class="purchase-info">
    <div class="purchase-info__inner">
        <div class="product img">
            <img src="{{ asset('storage/' .$item->item_img) }}" alt="{{ $item->item_name }}">
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
        <select wire:model = "paymentMethod"
                class="payment-select" name="payment-select" id="payment-select">
            <option disabled  selected>選択してください</option>
            <option value="1"  @if( old('condition')==1 ) selected @endif>コンビニ払い</option>
            <option value="2"  @if( old('condition')==2 ) selected @endif>カード支払い</option>
        </select>
    </div>

        <div class="purchase-address-area">
        <div class="purchase-address-area__header">
            <div class="purchase-address-area__title">配送先</div>
        <form class="purchase-address-update" action="{{ route('purchase.address.update', ['item'=> $item->id]) }}" method="get">
            @csrf
            @if (!old('change_address'))
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
            @else
            <div class="purchase-box__button">
                <input class="purchase-box__button-submit" type="submit" value="変更する">
                </div>
            </div>
            <div class="purchase-address">
                <h4>新しい配送先住所</h4>
                <label>郵便番号<input type="text" name="postal_code" value="{{ old('postal_code') }}"></label>
                <label>住所<input type="text" name="address" value="{{ old('address') }}"></label>
                <label>建物名<input type="text" name="building" value="{{ old('building') }}"></label>
            </div>
    

            @endif
{{--             <div class="purchase-box__button">
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
 --}}
        </form>
        </div>
</div>

<div class="confirm-surface">
    <table class="summary__table">
        <tr>
            <th>商品代金</th>
            <td><span class="yen">¥</span>
                <span class="price">{{ number_format($item->price) }}</span>
            </td>
        </tr>
        <tr>
            <th>支払い方法</th>
            <td>
                {{ $this->paymentLabel }}
            </td>
        </tr>
    </table>

    <button class="purchase-confirm__button-submit" wire:click = "purchase">
        購入する
    </button>
</div>
</div>
