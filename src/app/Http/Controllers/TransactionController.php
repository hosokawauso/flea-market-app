<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\TransactionRead;
use App\Http\Requests\TransactionMessageStoreRequest;

class TransactionController extends Controller
{
    public function show(Transaction $transaction)
    {
        $userId = auth()->id();

        abort_unless(
            $transaction->purchase->user_id === $userId ||
            $transaction->purchase->item->user_id === $userId,
            403
        );

        $transaction->load([
            'purchase.item',
            'purchase.item.seller',
            'messages' => fn($q) => $q->orderBy('id'),
        ]);

        $sidebarTransactions = Transaction::query()
            ->with('purchase.item')
            ->whereHas('purchase', function ($q) use ($userId) {
                $q->where('user_id', $userId)
                    ->orWhereHas('item', fn($q2) => $q2->where('user_id', $userId));
            })

            ->where('status', 'open')
            ->orderByDesc('last_message_at')
            ->get();

        $last = $transaction->messages->last();
        $lastMessageId = $last ? $last->id : null;
        TransactionRead::updateOrCreate(
            ['transaction_id' => $transaction->id, 'user_id' =>$userId],
            ['last_read_message_id' => $lastMessageId]
        );

        $isBuyer = ($transaction->purchase->user_id === $userId);
        $isSeller = ($transaction->purchase->item->user_id === $userId);

        $shouldOpenSellerRatingModal =
            $isSeller &&
            !is_null($transaction->buyer_rating) &&
            is_null($transaction->seller_rating);

        return view('transaction_chat', compact(
            'transaction',
            'sidebarTransactions',
            'isBuyer',
            'isSeller',
            'shouldOpenSellerRatingModal',
        ));
    }

    public function store(TransactionMessageStoreRequest $request, Transaction $transaction)
    {
        $userId = auth()->id();

        abort_unless(
            $transaction->purchase->user_id === $userId ||
            $transaction->purchase->item->user_id === $userId,
            403
        );

        $path = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('transaction_images', 'public');
        }

        $message = $transaction->messages()->create([
            'sender_id' => $userId,
            'body' => $request->input('body'),
            'image_path' => $path,
        ]);

        $transaction->update([
            'last_message_at' => now(),
        ]);

        return back()->with('success', '送信しました');
    }

    public function rateByBuyer(Request $request, Transaction $transaction)
    {
        $userId = auth()->id();

        // 購入者本人のみ
        abort_unless($transaction->purchase->user_id === $userId, 403);

        $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
        ]);

        $transaction->update([
            'buyer_rating'   => (int)$request->input('rating'),
            'buyer_rated_at' => now(),
            // 取引自体はまだ完了じゃない（出品者の評価待ち）
            'status' => 'waiting_seller_rating',
        ]);

        // ここで購入者は一覧へ（仕様どおり）
        return redirect('/mypage?page=transaction')->with('success', '評価を送信しました');
    }

    public function rateBySeller(Request $request, Transaction $transaction)
{
    $userId = auth()->id();

    // 出品者本人だけ
    abort_unless($transaction->purchase->item->user_id === $userId, 403);

    $request->validate([
        'rating' => ['required', 'integer', 'min:1', 'max:5'],
    ]);

    $transaction->update([
        'seller_rating'   => (int) $request->input('rating'),
        'seller_rated_at' => now(),
        // 両者評価が揃ったので完了
        'status'          => 'completed',
    ]);

    // purchaseも完了扱いにする（購入した商品タブに出す）
    $transaction->purchase->update([
        'status' => 'completed',
    ]);

    return redirect('/mypage?page=transaction')->with('success', '評価を送信しました');
}
}
