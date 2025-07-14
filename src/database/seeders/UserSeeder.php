<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = [
            [
                'name' => '山田一郎',
                'email' => '1@test.jp',
                'password' => Hash::make('hosokawa'),
                'postal_code' => 123-5678,
                'address' => '岡山県津山市真備3-6-2',
                'building' => 'サンハイツ桃太郎102',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'コマさん',
                'email' => '2@test.jp',
                'password' => Hash::make('hosokawa'),
                'postal_code' => '',
                'address' => '',
                'building' => '',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => '山田一郎',
                'email' => '3@test.jp',
                'password' => Hash::make('hosokawa'),
                'postal_code' => 123-5678,
                'address' => '神奈川県西丹沢',
                'building' => '',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

        ];

        DB::table('users')->insert($user);
    }
}
