<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Payment;
use App\MOdels\Purchase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;


class PurchaseTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        config()->set('payment.fake', true);
    }

    public function test_user_can_complete_purchase_process()
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $seller->id]);

        $addr = [
            'postal_code' =>'760-0080',
            'address' => '香川県高松市サンポート2-1',
            'building' => 'マリンタイムプラザ30階',
        ];

        $this->actingAs($buyer)
            ->from(route('purchase.confirm', ['item' => $item->id]))
            ->followingRedirects()
            ->post(route('purchase.address.update', ['item' => $item->id]), $addr)
            ->assertStatus(200);

        $this->followingRedirects()
            ->post(route('payment.checkout', ['item' => $item->id]),[
                'payment_method' => 'konbini',
            ])
            ->assertStatus(200);

        $purchase = Purchase::where('user_id', $buyer->id)
            ->where('item_id', $item->id)
            ->latest('id')
            ->first();

        $this->assertNotNull($purchase, 'Purchase was not created');

        $this->assertDatabaseHas('purchases', [
            'id' => $purchase->id,
            'user_id' => $buyer->id,
            'item_id' => $item->id,
            'purchase_postal_code' => $addr['postal_code'],
            'purchase_address' => $addr['address'],
            'purchase_building' => $addr['building'],
        ]);

    }

    public function test_sold_label_is_displayed_for_purchased_item_on_index_page()
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $seller->id]);

        $addr = [
            'postal_code' =>'760-0080',
            'address' => '香川県高松市サンポート2-1',
            'building' => 'マリンタイムプラザ30階',
        ];

        $this->actingAs($buyer)
            ->from(route('purchase.confirm', ['item' => $item->id]))
            ->followingRedirects()
            ->post(route('purchase.address.update', ['item' => $item->id]), $addr)
            ->assertStatus(200);

        $this->followingRedirects()
            ->post(route('payment.checkout', ['item' => $item->id]),[
                'payment_method' => 'konbini',
            ])
            ->assertStatus(200);
        
        $item->refresh();
        if (!$item->is_sold) {
            $item->is_sold = true;
            $item->save();
        }

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSeeText($item->item_name);
        $response->assertSeeText('Sold');
    }

    public function test_purchased_item_is_displayed_on_my_page()
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $seller->id]);

        $addr = [
            'postal_code' =>'760-0080',
            'address' => '香川県高松市サンポート2-1',
            'building' => 'マリンタイムプラザ30階',
        ];

        $this->actingAs($buyer)
            ->from(route('purchase.confirm', ['item' => $item->id]))
            ->followingRedirects()
            ->post(route('purchase.address.update', ['item' => $item->id]), $addr)
            ->assertStatus(200);

        $this->followingRedirects()
            ->post(route('payment.checkout', ['item' => $item->id]),[
                'payment_method' => 'konbini',
            ])
            ->assertStatus(200);

        $response = $this->get('/mypage?page=buy');
        $response->assertStatus(200);
        $response->assertSeeText($item->item_name);

        if(!empty($item->item_img)){
            $response->assertSee(e(asset('storage/' . $item->item_img)));
        }
    }
}