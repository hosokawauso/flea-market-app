<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use App\Models\Comment;
use App\Models\Favorite;
use Tests\TestCase;

class ItemViewTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_item_detail_displays_all_information()
    {
        $user = User::factory()->create([
            'name' => 'コメントユーザー',
            'profile_img' => 'profile_imgs/sample.png'
        ]);

        $item = Item::factory()->create([
            'item_name' => 'テスト商品',
            'brand_name' => 'ブランドA',
            'price' => 1000,
            'description' =>'これは商品の説明です',
            'item_img' => 'item_imgs/default.png',
            'condition' => 3,
            'is_sold' => false,
        ]);

        $categories = Category::factory()->count(2)->create();

        $item->categories()->attach($categories->pluck('id'));

        $item->favoritedBy()->attach([
            'user_id' => $user->id
        ]);

        $item->comments()->create([
            'user_id' => $user->id,
            'body' => 'これは商品に対するコメントです',
        ]);

        $response = $this->get('item/' .$item->id);

        $response->assertStatus(200);
        $response->assertSee($item->item_name);
        $response->assertSee($item->brand_name);
        $response->assertSee(number_format($item->price));
        $response->assertSee($item->description);
        $response->assertSee('item_imgs/default.png');
        $response->assertDontSee('Sold');

        foreach($categories as $category) {
            $response->assertSee($category->content);
        }

        //「いいね」の数
        $response->assertSeeText('1');

        $response->assertSee('これは商品に対するコメントです');
        $response->assertSee('コメントユーザー');
    }

    public function test_item_detail_displays_all_attached_categories()
    {
        $item = Item::factory()->create();

        $categories = Category::factory()->count(14)->create();

        $item->categories()->attach($categories->pluck('id'));

        $response = $this->get("/item/{$item->id}");

        $response->assertStatus(200);

        foreach ($categories as $category) {
            $response->assertSee($category->content);
        }
    }
}