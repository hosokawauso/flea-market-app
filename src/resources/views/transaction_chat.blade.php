@extends('layouts.chat')

@section('css')
  <link rel="stylesheet" href="{{ asset('css/transaction_chat.css') }}">
@endsection

@section('content')
  <div class="transaction-chat">
    {{--左のサイドバー--}}
    <aside class="transaction-chat__sidebar">
      <h3 class="sidebar__title">その他の取引</h3>

      <div class="sidebar__list">
        @foreach($sidebarTransactions as $t)
          <a class="sidebar__item {{ $transaction->id === $transaction->id ? 'is-active' : '' }}"
            href="{{ route('transactions.show', ['transaction' => $t->id])}}">
            <div class="sidebar__thumb">
              <img src="{{ asset('storage/' . $t->purchase->item->item_img) }}"
                  alt="{{ $transaction->purchase->item->item_name }}">
            </div>

            <div class="sidebar__meta">
              <p class="sidebar__name">
                {{ $transaction->purchase->item->item_name }}
              </p>
            </div>
          </a>
        @endforeach
      </div>
    </aside>

    {{--右の本文--}}
    <section class="transaction-chat__main">
      <div class="chat-header">
        <div class="chat-header__left">
          <div class="chat-header__avatar"></div>
          <h2 class="chat-header__title">
            「{{ $item->name ?? 'ユーザー名' }}」さんとの取引画面
          </h2>
        </div>
        @if($isBuyer)
          <button type="button" class="chat-header__complete" id="open-complete-modal">
          取引を完了する
          </button>
        @endif
      </div>

      {{--商品情報--}}
      <div class="chat-item">
        <div class="chat-item__img">
          <img src="{{ asset('storage/' .$transaction->purchase->item->item_img)  }}" alt="{{ $transaction->purchase->item->item_name }}">
        </div>
        <div class="chat-item__info">
          <p class="chat-item__name">{{ $transaction->purchase->item->item_name }}</p>
          <p class="chat-item__price">商品価格</p>
          <p class="chat-item__priceValue">¥{{ number_format($transaction->purchase->item->price) }}</p>
        </div>
      </div>

      {{--メッセージ一覧--}}
    <div class="chat-messages" id="chat-scroll">
      @forelse($transaction->messages as $m)
        @php 
          $isMe = ($m->sender_id === auth()->id());
        @endphp

        <div class="msg {{ $isMe ? 'is-me' : 'is-other' }}">
          <div class="msg__bubble">
            <p class="msg__name">{{ $isMe ? 'あなた' : ($partner->name ?? 'ユーザー名') }}</p>

            @if($m->body)
              <p class="msg__text">{{ $m->body }}</p>
            @endif

            @if($m->image_path)
              <img class="msg__img" src="{{ asset('storage/' . $m->image_path) }}" alt="image">
            @endif

            <div class="msg__meta">
              <span class="msg__time">{{ $m->created_at ? $m->created_at->format('H:i') : '' }}</span>

              {{-- 編集/削除（自分の投稿のみ） --}}
              @if($isMe)
                <button type="button" class="msg__action" onclick="toggleEdit({{ $m->id }})">
                  編集
                </button>

                <form id="edit-form-{{ $m->id }}"
                      method="POST"
                      action="{{ route('transactions.messages.update', ['message' => $m->id]) }}"
                      style="display:none; margin-top:6px;">
                  @csrf
                  @method('PATCH')

                  <input type="text"
                        name="body"
                        value="{{ $m->body }}"
                        class="msg__editInput">

                  <button type="submit" class="msg__action">保存</button>
                </form>
              @endif
              @if($isMe)
                <form method="POST"
                      action="{{ route('transactions.messages.destroy', ['message' => $m->id]) }}"
                      style="display:inline;">
                  @csrf
                  @method('DELETE')
                  <button type="submit"
                          class="msg__action"
                          onclick="return confirm('削除しますか？')">
                    削除
                  </button>
                </form>
              @endif

            </div>
          </div>

          <div class="msg__avatar"></div>
        </div>
      @empty
        <p class="chat-messages__empty">メッセージはまだありません</p>
      @endforelse
    </div>


      {{-- エラー（FN008） --}}
      @if($errors->any())
        <div class="chat-input__errors">
          @foreach($errors->all() as $error)
            <p class="chat-input__error">{{ $error }}</p>
          @endforeach
        </div>
      @endif


    @if(session('success'))
      <div class="chat-input__success">
      {{ session('success') }}
      </div>
    @endif

    {{-- 入力欄 --}}
    <form class="chat-input" method="POST" action="{{ route('transactions.messages.store', $transaction) }}" enctype="multipart/form-data">
      @csrf

      {{-- エラー（FN008） --}}
      @if($errors->any())
        <div class="chat-input__errors">
          @foreach($errors->all() as $error)
            <p class="chat-input__error">{{ $error }}</p>
          @endforeach
        </div>
      @endif

      <input class="chat-input__text"
            type="text"
            name="body"
            placeholder="取引メッセージを記入してください"
            value="{{ old('body') }}">

      <label class="chat-input__fileBtn">
        画像を追加
        <input type="file" name="image" accept=".png,.jpeg,.jpg" hidden>
      </label>

      <button class="chat-input__send" type="submit" aria-label="送信">
        ▶
      </button>
    </form>

  </section>
</div>


{{-- 完了モーダル--}}
<div class="modal" id="complete-modal" aria-hidden="true">
  <div class="modal__backdrop"></div>

  <div class="modal__panel">
    <h3 class="modal__title">取引が完了しました。</h3>
    <p class="modal__sub">今回の取引相手はどうでしたか？</p>

    <input type="hidden"
          id="auto-open-seller-rating"
          value="{{ $shouldOpenSellerRatingModal ? '1' : '0' }}">

    @php
      $rateRoute = $isBuyer
          ? route('transactions.rate.buyer', ['transaction' => $transaction->id])
          : route('transactions.rate.seller', ['transaction' => $transaction->id]);
    @endphp

    <form method="POST" action="{{ $rateRoute }}" id="rate-form">
      @csrf

      <div class="stars">
        <input type="hidden" name="rating" id="rating-value" value="">

        <button type="button" class="star" data-value="1">★</button>
        <button type="button" class="star" data-value="2">★</button>
        <button type="button" class="star" data-value="3">★</button>
        <button type="button" class="star" data-value="4">★</button>
        <button type="button" class="star" data-value="5">★</button>
      </div>
<div>action: {{ $rateRoute }}</div>
      <button type="submit">評価を送信</button>
    </form>
  </div>
</div>

  <script>
    document.addEventListener('DOMContentLoaded', function () {

      /*モーダル開閉*/
    const modal = document.getElementById('complete-modal');
    if (!modal) return;

    const close = () => modal.setAttribute('aria-hidden', 'true');
    const open  = () => modal.setAttribute('aria-hidden', 'false');

    // ① 購入者ボタンで開く（購入者だけボタンが存在）
    const openBtn = document.getElementById('open-complete-modal');
    if (openBtn) {
      openBtn.addEventListener('click', open);
    }

    // ② 背景クリックで閉じる（どちらの立場でも有効）
    const backdrop = modal.querySelector('.modal__backdrop');
    if (backdrop) {
      backdrop.addEventListener('click', close);
    }

    // ③ 出品者：条件を満たしたら自動で開く（openBtn不要）
    const auto = document.getElementById('auto-open-seller-rating');
    if (auto && auto.value === '1') {
      open();
    }
    /*メッセージ編集切り替え*/
    window.toggleEdit = function(id){
      const f = document.getElementById('edit-form-' + id);
      if (!f) return;

      f.style.display =
        (f.style.display === 'none' || f.style.display === '')
          ? 'block'
          : 'none';
    };

    /*星評価 */
    const stars = document.querySelectorAll('.star');
    const ratingInput = document.getElementById('rating-value');

    if (stars.length && ratingInput) {
      stars.forEach(star => {
        star.addEventListener('click', function () {
          const value = this.dataset.value;
          ratingInput.value = value;

          stars.forEach(s => {
            s.classList.remove('is-active');
            if (s.dataset.value <= value) {
              s.classList.add('is-active');
            }
          });
        });
      });
    }

  });
  </script>
@endsection