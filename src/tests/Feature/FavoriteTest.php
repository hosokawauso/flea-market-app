<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;

class FavoriteTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_favorite_count_increases_when_user_favorites_item()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $response = $this->actingAs($user)->post("/item/{$item->id}/favorites");
        $response->assertRedirect();

        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $response = $this->get("/item/{$item->id}");

        //いいねの数
        $response->assertSeeText('1');
    }

    public function test_favorite_icon_is_changed_color_when_user_click_icon()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        //あらかじめ「いいね」を登録する
        $user->favoriteItems()->attach($item->id);

        $response = $this->actingAs($user)->get("/item/{$item->id}");

        $response->assertSee('img/selected.jpeg');
    }

    public function test_favorite_count_decrease_when_user_unfavorite_item()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $user->favoriteItems()->attach($item->id);

        $response = $this->actingAs($user)->post("/item/{$item->id}/favorites");
        $response->assertRedirect();

        $this->assertDatabaseMissing('favorites', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $response = $this->get("/item/{$item->id}");

        $response->assertSeeText('0');
    }
}
