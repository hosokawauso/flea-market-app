<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;
use App\Models\TransactionRead;
use App\Models\TransactionMessage;



class UserController extends Controller
{



    public function show()
    {
        return view('profile', ['user'=>Auth::user()]);
    }

    public function update(ProfileRequest $request)
    {
        $user = Auth::user();

        $user->fill($request->only(['name', 'postal_code', 'address', 'building']));

        if($request->hasFile('profile_img')) {
            $path = $request->file('profile_img')->store('profile_imgs', 'public');
            $user->profile_img = $path;
        }

        $user->is_profile_set = true;
        $user->save();

        return redirect('/mypage');
    }

    public function mypage(Request $request)
    {
        $user = Auth::user();
        $page = $request->query('page', 'sell');

        $sellingItems = $user->items ?? collect();
        $purchasedItems = $user->purchases()
            ->where('status', 'completed')
            ->with('item')
            ->get()
            ->pluck('item');

        $transactions = collect();
        $totalUnread = 0;

        $userId = $user->id;

        // 自分が関係する「取引中」の取引IDを全部取得（IDだけ）
        $transactionIds = Transaction::query()
            ->whereHas('purchase', function ($q) use ($userId) {
                $q->where('user_id', $userId)
                ->orWhereHas('item', fn($q2) => $q2->where('user_id', $userId));
            })
            ->whereIn('status', ['open', 'waiting_seller_rating'])
            ->pluck('id');

        // 既読位置をまとめて取得（transaction_id => last_read_message_id）
        $readMap = TransactionRead::where('user_id', $userId)
            ->whereIn('transaction_id', $transactionIds)
            ->pluck('last_read_message_id', 'transaction_id');

        // 未読総数を合計
        $totalUnread = 0;
        foreach ($transactionIds as $tid) {
            $lastReadId = (int) ($readMap[$tid] ?? 0);

            $totalUnread += TransactionMessage::query()
                ->where('transaction_id', $tid)
                ->where('sender_id', '!=', $userId)
                ->where('id', '>', $lastReadId)
                ->count();
        }

        // 平均評価
        $avgSeller = Transaction::query()
            ->whereNotNull('buyer_rating') // 出品者が受け取る評価
            ->whereHas('purchase.item', function ($q) use ($user) {
                $q->where('user_id', $user->id); // 自分が出品者
            })
            ->avg('buyer_rating');

        $countSeller = Transaction::query()
            ->whereNotNull('buyer_rating')
            ->whereHas('purchase.item', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->count();

        $avgBuyer = Transaction::query()
            ->whereNotNull('seller_rating') // 購入者が受け取る評価
            ->whereHas('purchase', function ($q) use ($user) {
                $q->where('user_id', $user->id); // 自分が購入者
            })
            ->avg('seller_rating');

        $countBuyer = Transaction::query()
            ->whereNotNull('seller_rating')
            ->whereHas('purchase', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->count();

        // 合算して平均（どちらも0件なら null）
        $totalCount = $countSeller + $countBuyer;

        if ($totalCount > 0) {
            $sum = ($avgSeller * $countSeller) + ($avgBuyer * $countBuyer);
            $avgAll = $sum / $totalCount;
            $avgRatingInt = (int) round($avgAll); // 四捨五入して整数
        } else {
            $avgRatingInt = null;
        }

        if ($page === 'transaction') {
            $userId = $user->id;

            $transactions = Transaction::query()
                ->with(['purchase.item'])
                ->whereHas('purchase', function ($q) use ($userId) {
                    $q->where('user_id', $userId)
                    ->orWhereHas('item', function ($q2) use ($userId) {
                        $q2->where('user_id', $userId);
                    });
                })
                ->whereIn('status', ['open', 'waiting_seller_rating'])
                ->orderByDesc('last_message_at')
                ->orderByDesc('id')
                ->get();

            $transactions->each(function ($t) use ($userId) {
                $lastReadId = (int) ($t->reads()
                    ->where('user_id', $userId)
                    ->value('last_read_message_id') ?? 0);

                $t->unread_count = $t->messages()
                    ->where('sender_id', '!=', $userId)
                    ->where('id', '>', $lastReadId)
                    ->count();

                /* $t->unread_count = $t->messages()
                    ->when($lastReadId, function ($q) use ($lastReadId) {
                        $q->where('id', '>', $lastReadId);
                    })
                    ->where('sender_id', '!=', $userId)
                    ->count(); */
            });

            /* $totalUnread = $transactions->sum('unread_count'); */
        }

        return view('mypage', compact(
            'user',
            'page',
            'sellingItems',
            'purchasedItems',
            'transactions',
            'totalUnread',
            'avgRatingInt',
        ));
    }
}
