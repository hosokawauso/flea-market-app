<?php

namespace Database\Seeders;

use App\Models\Payment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $payment = [
            ['method' => 'コンビニ払い', 'amount' => 0],
            ['method' => 'カード支払い', 'amount' => 0],
        ];

        DB::table('payments')->insert($payment);
    }

    
}
