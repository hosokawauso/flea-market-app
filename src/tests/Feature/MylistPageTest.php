<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MylistPageTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_mylist_shows_only_items_user_has_favorite()
    {
        $user = User::factory()->create();

        $item1 = Item::factory()->create(['item_name' => 'テスト商品1']);
        $item2 = Item::factory()->create(['item_name' => 'テスト商品2']);
        $item3 = Item::factory()->create(['item_name' => 'テスト商品3']);

        $user->favoriteItems()->attach([$item1->id, $item3->id]);

        $response = $this->actingAs($user)->get('/?page=mylist');

        $response->assertSee($item1->item_name);
        $response->assertSee($item3->item_name);
        $response->assertDontSee($item2->item_name);
    }

    public function test_sold_items_show_sold_label()
    {
        $user = User::factory()->create();

        $soldItem = Item::factory()->create([
            'item_name' => '購入済み商品',
            'is_sold' => true,
        ]);

        $availableItem = Item::factory()->create([
            'item_name' => '購入可能な商品',
            'is_sold' => false,
        ]);

        $user->favoriteItems()->attach([$soldItem->id, $availableItem->id]);

        $response = $this->actingAs($user)->get('/?page=mylist');

        $response->assertSee('Sold');
        $response->assertSeeText($soldItem->item_name);
        $response->assertSeeText($availableItem->item_name);
    }

    public function test_logged_in_user_does_not_see_own_items_on_mylist_page()
    {
        $user = User::factory()->create();

        $ownItem = Item::factory()->create([
            'user_id' => $user->id,
            'item_name' => '自分の商品',
        ]);

        $otherItem = Item::factory()->create([
            'item_name' => '他の方の商品',
        ]);

        $user->favoriteItems()->attach($otherItem->id);

        $response = $this->actingAs($user)->get('/?page=mylist');

        $response->assertDontSee($ownItem->item_name);
        $response->assertSee($otherItem->item_name);
    }

    public function test_guest_sees_empty_mylist_page()
    {
        $response = $this->get('/?page=mylist');

        $response->assertStatus(200);

        $response->assertSee('マイリストに登録した商品はありません');
    }
}
