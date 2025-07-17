<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use App\Models\Item;
use App\Models\Category;
use App\Models\User;
use Tests\TestCase;

class CatalogPageTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_guest_can_view_all_items_with_basic_info()
    {
        $items = Item::factory()->count(10)->create([
            'item_img' => 'default.png',
            'is_sold' => false,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);

        foreach ($items as $item) {
            $response->assertSee($item->item_name);
        }
    }

    public function test_sold_items_show_sold_label()
    {
        $soldItem = Item::factory()->create([
            'item_name' => '購入済み商品',
            'is_sold' => true,
        ]);

        $availableItem = Item::factory()->create([
            'item_name' => '購入可能な商品',
            'is_sold' => false,
        ]);

        $response = $this->get('/');

        $response->assertSee('Sold');
        $response->assertSee($soldItem->item_name);
        $response->assertSee($availableItem->item_name);
    }

    public function test_logged_in_user_does_not_see_their_own_items()
    {
        $user = User::factory()->create();

        $ownItem = Item::factory()->create([
            'user_id' => $user->id,
            'item_name' =>'自分の商品'
        ]);

        $otherItem = Item::factory()->create([
            'item_name' => '他の方の商品',
        ]);

        $response = $this->actingAs($user)->get('/');

        $response->assertDontSee($ownItem->item_name);
        $response->assertSee($otherItem->item_name);
    }
}
