<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [
            [
                'item_name' => '腕時計',
                'brand_name' => 'ARMANI',
                'price' => 15,000,
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'item_img' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Armani+Mens+Clock.jpg',
                'condition' => 1,
            ],
            [
                'item_name' => 'HDD',
                'price' => 5,000,
                'description' => '高速で信頼性の高いハードディスク',
                'item_img' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/HDD+Hard+Disk.jpg',
                'condition' => 2,
            ],
            [
                'item_name' => '玉ねぎ３束',
                'price' => 300,
                'description' => '新鮮な玉ねぎの３束セット',
                'item_img' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/iLoveIMG+d.jpg',
                'condition' => 3,
            ],
            [
                'item_name' => '革靴',
                'price' => 4,000,
                'description' => 'クラシックなデザインの革靴',
                'item_img' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Leather+Shoes+Product+Photo.jpg',
                'condition' => 4,
            ],
            [
                'item_name' => 'ノートPC',
                'price' => 45,000,
                'description' => '高性能なノートPC',
                'item_img' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Living+Room+Laptop.jpg',
                'condition' => 1,
            ],
            [
                'item_name' => 'マイク',
                'price' => 8,000,
                'description' => '高音質のレコーディング用マイク',
                'item_img' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Music+Mic+4632231.jpg',
                'condition' => 2,
            ],
            [
                'item_name' => 'ショルダーバッグ',
                'price' => 3,500,
                'description' => 'おしゃれなショルダーバッグ',
                'item_img' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Purse+fashion+pocket.jpg',
                'condition' => 3,
            ],
            [
                'item_name' => 'タンブラー',
                'price' => 500,
                'description' => '使いやすいタンブラー',
                'item_img' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Tumbler+souvenir.jpg',
                'condition' => 4,
            ],
            [
                'item_name' => 'コーヒーミル',
                'price' => 4000,
                'description' => '手動のコーヒーミル',
                'item_img' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Waitress+with+Coffee+Grinder.jpg',
                'condition' => 1,
            ],
            [
                'item_name' => 'メイクセット',
                'price' => 2,500,
                'description' => '便利なメイクアップセット',
                'item_img' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/%E5%A4%96%E5%87%BA%E3%83%A1%E3%82%A4%E3%82%AF%E3%82%A2%E3%83%83%E3%83%95%E3%82%9A%E3%82%BB%E3%83%83%E3%83%88.jpg',
                'condition' => 2,
            ],

        ];
    }
}
