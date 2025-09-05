<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use Illuminate\Support\Facades\DB;


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
                'user_id' => 1,
                'item_name' => '腕時計',
                'brand_name' => 'ARMANI',
                'price' => 15000,
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'item_img' => 'item_imgs/Armani+Mens+Clock.jpg',
                'condition' => 1,
                'is_sold' => false,
                'categories' => [1, 4, 12],
            ],
            [
                'user_id' => 2,
                'item_name' => 'HDD',
                'brand_name' => '',
                'price' => 5000,
                'description' => '高速で信頼性の高いハードディスク',
                'item_img' => 'item_imgs/HDD+Hard+Disk.jpg',
                'condition' => 2,
                'is_sold' => false,
                'categories' => [2, 8],
            ],
            [
                'user_id' => 3,
                'item_name' => '玉ねぎ３束',
                'brand_name' => '',
                'price' => 300,
                'description' => '新鮮な玉ねぎの３束セット',
                'item_img' => 'item_imgs/iLoveIMG+d.jpg',
                'condition' => 3,
                'is_sold' => false,
                'categories' => [11],
            ],
            [
                'user_id' => 1,
                'item_name' => '革靴',
                'brand_name' => '',
                'price' => 4000,
                'description' => 'クラシックなデザインの革靴',
                'item_img' => 'item_imgs/Leather+Shoes+item+Photo.jpg',
                'condition' => 4,
                'is_sold' => false,
                'categories' => [1, 5],
            ],
            [
                'user_id' => 2,
                'item_name' => 'ノートPC',
                'brand_name' => '',
                'price' => 45000,
                'description' => '高性能なノートPC',
                'item_img' => 'item_imgs/Living+Room+Laptop.jpg',
                'condition' => 1,
                'is_sold' => false,
                'categories' => [2, 8, 9],
            ],
            [
                'user_id' => 3,
                'item_name' => 'マイク',
                'brand_name' => 'SONY',
                'price' => 8000,
                'description' => '高音質のレコーディング用マイク',
                'item_img' => 'item_imgs/Music+Mic+4632231.jpg',
                'condition' => 2,
                'is_sold' => false,
                'categories' => [3, 9, 13],
            ],
            [
                'user_id' => 1,
                'item_name' => 'ショルダーバッグ',
                'brand_name' => 'COACH',
                'price' => 3500,
                'description' => 'おしゃれなショルダーバッグ',
                'item_img' => 'item_imgs/Purse+fashion+pocket.jpg',
                'condition' => 3,
                'is_sold' => false,
                'categories' => [1, 4],
            ],
            [
                'user_id' => 2,
                'item_name' => 'タンブラー',
                'brand_name' => 'NORTH FACE',
                'price' => 500,
                'description' => '使いやすいタンブラー',
                'item_img' => 'item_imgs/Tumbler+souvenir.jpg',
                'condition' => 4,
                'is_sold' => false,
                'categories' => [1,10],
            ],
            [
                'user_id' => 3,
                'item_name' => 'コーヒーミル',
                'brand_name' => '',
                'price' => 4000,
                'description' => '手動のコーヒーミル',
                'item_img' => 'item_imgs/Waitress+with+Coffee+Grinder.jpg',
                'condition' => 1,
                'is_sold' => false,
                'categories' => [3, 10],
            ],
            [
                'user_id' => 1,
                'item_name' => 'メイクセット',
                'brand_name' => '資生堂',
                'price' => 2500,
                'description' => '便利なメイクアップセット',
                'item_img' => 'item_imgs/Makeup+Set.jpg',
                'condition' => 2,
                'is_sold' => false,
                'categories' => [1, 4, 6],
            ],

        ];

        foreach ($items as $data) {
            $categoryIds = $data['categories'];
            unset($data['categories']);

            $item = Item::create($data);

            $item->categories()->attach($categoryIds);
        }
    }
}
