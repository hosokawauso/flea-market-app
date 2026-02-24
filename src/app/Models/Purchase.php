<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Transaction;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'item_id',
        'status',
        'purchase_postal_code',
        'purchase_address',
        'purchase_building',
    ];
    
    protected static function booted()
    {
        static::created(function (Purchase $purchase) {
            Transaction::firstOrCreate(
                ['purchase_id' => $purchase->id],
                ['status' => 'open', 'last_message_at' => now()]
            );
        });
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'purchase_id', 'id');
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class, 'purchase_id');
    }
}
