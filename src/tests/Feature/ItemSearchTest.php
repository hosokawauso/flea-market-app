<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ItemSearchTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_user_can_search_items_by_item_name_keyword()
    {
        $matchItem = Item::factory()->create([
            'item_name' => 'テスト商品',
        ]);

        $noMatch = Item::factory()->create([
            'item_name' => '別の商品',
        ]);

        $response = $this->get('/search?keyword=テスト');

        $response->assertStatus(200);
        $response->assertSee($matchItem->item_name);
        $response->assertDontSee($noMatch->item_name);
    }
}
