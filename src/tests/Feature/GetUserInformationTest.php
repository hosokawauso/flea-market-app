<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GetUserInformationTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_user_get_required_information()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create([
            'user_id' => $user->id,
            'item_img' => 'item.imgs/default.png',
        ]);

        $sellingItems = $user->items ?? collect();
        $purchaseItems = $user->purchases()->with('item')->get()->pluck('item');

        $response = $this->actingAs($user)->get('/mypage');

        $response->assertStatus(200);
        $response->assertSee($user->profile_img);
        $response->assertSee($user->name);

        foreach ($sellingItems as $item) {
            $response->assertSee([
                'default.png',
                $item->item_name,
            ]);
        }

        foreach ($purchaseItems as $item) {
            $response->assertSee([
                'default.png',
                $item->item_name,
            ]);
        }

    }
}
