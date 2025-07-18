<div class="purchase-screen">
    <div class="purchase-info">
            <div class="purchase-info__inner">
                <div class="product-image">
                    @if (Str::startsWith($item->item_img, 'http'))
                        <img src="{{ $item->item_img }}" alt="{{ $item->item_name }}">
                    @else
                        <img src="{{ asset('storage/' .$item->item_img) }}" alt="{{ $item->item_name }}">
                    @endif
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
                    <option value="" disabled  selected>選択してください</option>
                        @foreach($payments as $payment)
                        <option value="{{ $payment->method }}">{{ $payment->method }}</option>
                        @endforeach
                </select>
            </div>
        </div>

        <div class="purchase-address-area">

                    <div class="purchase-address-area__title">配送先</div>


                    <div class="purchase-box__button">
                    <a href="{{ route('purchase.address.edit',['item' => $item->id] )}}">変更する</a>
                    </div>

                    @if ($changeAddress)
                    @include('purchase-address', ['purchase' => $purchase])

                    @else
                    <div class="purchase-address">
                    <div class="postal-code">
                        <span>〒</span>
                        {{ $purchase['postal_code'] }}
                    </div>
                    <div class="address">
                    {{ $purchase['address'] }}{{ $purchase['building'] }}
                    </div>
                    </div>
                    @endif
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
                        {{ $this->paymentMethod ?: '未選択' }}
                    </td>
                </tr>
            </table>

            <button class="purchase-confirm__button-submit" wire:click = "purchase">
                購入する
            </button>
        </div>
    </div>
</div>





