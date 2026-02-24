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
            ['email' => 'test1@example.com'],
            [
                'id' => 1,
                'name' => 'テスト太郎',
                'password' => Hash::make('password'),
                'profile_img' => null,
                'postal_code' => '123-4567',
                'address' => '高松市浜ノ町',
                'building' => 'レジデンス101',
                'is_profile_set' => true,
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'test2@example.com'],
            [
                'id' => 2,
                'name' => 'テスト花子',
                'password' => Hash::make('password'),
                'profile_img' => null,
                'postal_code' => '789-1123',
                'address' => '栃木県宇都宮市1-1',
                'building' => '',
                'is_profile_set' => true,
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'test3@example.com'],
            [
                'id' => 3,
                'name' => 'キム・テスト',
                'password' => Hash::make('password'),
                'profile_img' => null,
                'postal_code' => '868-0259',
                'address' => '岐阜県岐阜市3-25',
                'building' => 'コーポラス1011',
                'is_profile_set' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}