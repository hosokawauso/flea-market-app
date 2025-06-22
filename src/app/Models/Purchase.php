<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $guarded = [
        'id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    /* public function purchaseAddress()
    {
        return $this->hasOne(PurchaseAddress::class);
    } */

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
