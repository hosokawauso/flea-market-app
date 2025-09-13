<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
    'purchase_id',
    'amount',
    'currency',
    'method',
    'status',
    'checkout_session_id',
    'payment_intent_id',
    'expires_at',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'payment_id', 'id');
    }

}
