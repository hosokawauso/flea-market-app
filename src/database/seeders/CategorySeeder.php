<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            ['content' => 'ファッション'],
            ['content' => '家電'],
            ['content' => 'インテリア'],
            ['content' =>  'レディース'],
            ['content' => 'メンズ'],
            ['content' => 'コスメ'],
            ['content' =>  '本'],
            ['content' => 'ゲーム'],
            ['content' => 'スポーツ'],
            ['content' => 'キッチン'],
            ['content' => 'ハンドメイド'],
            ['content' => 'アクセサリー'],
            ['content' => 'おもちゃ'],
            ['content' => 'ベビー・キッズ'],
        ];
        foreach ($categories as $row) {
            Category::firstOrCreate(['content' => $row['content']]);
        }
    }
}
