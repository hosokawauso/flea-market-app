<?php

namespace App\Mail;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TransactionCompletedByBuyerMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $transaction;

    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;

        $this->transaction->loadMissing([
            'purchase.item',
            'purchase.user',
            'purchase.item.user',
        ]);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $itemName = $this->transaction->purchase->item->item_name ?? '商品';

        return $this->subject("取引完了のお知らせ：{$itemName}")
            ->view('emails.transaction_completed_by_buyer');

    }
}
