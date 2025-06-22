<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

/*     const METHOD_CONVENIENCE = 1;
    const METHOD_CARD = 2;

    public static function getMethodLabels()
    {
        return [
            self::METHOD_CONVENIENCE => 'コンビニ払い',
            self::METHOD_CARD => 'カード支払い',
        ];
    }
 */

    protected $guarded = [
        'method',
        'amount',
        'stripe_payment_id'
    ];

    public function purchase()
    {
        return $this->hasOne(Purchase::class, 'payment_id');
    }

}
