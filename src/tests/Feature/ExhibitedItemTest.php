<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;


class ExhibitedItemTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_user_sell_items_with_required_information()
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $categories = Category::factory()->count(3)->create();

        $response = $this->actingAs($user)->post('/sell', [
            'item_name' => 'テスト商品',
            'brand_name' => 'ブランドA',
            'price' => 1000,
            'description' => 'これは商品の説明です',
            'item_img' => UploadedFile::fake()->image('test.png'),
            'condition' => 3,
            'is_sold' => false,
            'category' => $categories->pluck('id')->toArray(),
        ]);


        $this->assertDatabaseHas('items', [
            'user_id' =>$user->id,
            'item_name' => 'テスト商品',
            'brand_name' => 'ブランドA',
            'price' => 1000,
            'description' => 'これは商品の説明です',
            'condition' => 3,
            'is_sold' => false,
        ]);

        $item = Item::first();

        foreach ($categories as $category) {
            $this->assertDatabaseHas('category_item', [
                'category_id' => $category->id,
            ]);
        }
    }
}
