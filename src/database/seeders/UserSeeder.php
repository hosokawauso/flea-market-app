<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => "テスト太郎",
                'password'          => Hash::make('password'),
                'profile_img'       => null,
                'postal_code'       => '123-4567',
                'address'           => '高松市浜ノ町',
                'building'          => 'レジデンス101',
                'is_profile_set'    => true,
                'email_verified_at' => now(),
            ]
        );

        User::factory()->count(4)->create();
    }
}
