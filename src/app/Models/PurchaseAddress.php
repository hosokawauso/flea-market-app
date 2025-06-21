<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'postal_code',
        'address',
        'building',
    ];

    protected $guarded = [
        'id'
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }
}
