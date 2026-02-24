<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransactionMessage;

class TransactionMessageController extends Controller
{
    public function update(Request $request, TransactionMessage $message)
    {
        abort_unless($message->sender_id === auth()->id(), 403);

        $request->validate([
            'body' => ['required', 'string', 'max:400'],
        ]);

        $message->update([
            'body' => $request->body,
            'edited_at' => now(),
        ]);

        $message->transaction->update(['last_message_at' => now()]);

        /* dd($request->all(), $message->body); */

        return redirect()
            ->route('transactions.show', ['transaction' => $message->transaction_id])
            ->with('success', '更新しました');
    }

    public function destroy(TransactionMessage $message)
    {
        abort_unless($message->sender_id === auth()->id(), 403);

        $message->delete();

        return back()->with('success', '削除しました');
    }
}
