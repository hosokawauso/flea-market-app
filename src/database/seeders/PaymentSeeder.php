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
            ['amount' => 0,
            'method' => 'konbini',
            'currency' => 
        ], ['amount' => 0,
            'method' => 'card', 
        ],
        ];

        DB::table('payments')->insert($payment);
    }

    
}
