<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_id',
        'status',
        'last_message_at',
        'completed_by_buyer_at',
        'completed_by_seller_at',
        'buyer_rating',
        'seller_rating',
        'buyer_rated_at',
        'seller_rated_at',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'purchase_id');

    }

    public function messages()
    {
        return $this->hasMany(TransactionMessage::class);
    }

    public function reads()
    {
        return $this->hasMany(TransactionRead::class);
    }
}