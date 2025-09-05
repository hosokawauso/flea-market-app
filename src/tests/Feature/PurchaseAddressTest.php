<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Payment;
use App\Models\Purchase;


class PurchaseAddressTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_purchase_address_is_displayed_purchase_page()
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $seller->id]);

        $payload = [
            'postal_code' => '123-4567',
            'address' => '香川県高松市浜ノ町',
            'building' => 'サンポートタワー15階',
        ];

        $this->actingAs($buyer)
            ->from(route('purchase.confirm', ['item' => $item->id]))
            ->followingRedirects()
            ->post(route('purchase.address.update', ['item' => $item->id]), $payload)
            ->assertStatus(200);

        $this->get(route('purchase.confirm', ['item' => $item->id]))
            ->assertStatus(200)
            ->assertSee($payload['postal_code'])
            ->assertSee($payload['address'])
            ->assertSee($payload['building']);
    }

    public function test_purchase_address_is_stored_with_purchased_item()
    {
        $seller = User::factory()->create();
        $buyer  = User::factory()->create();
        $item   = Item::factory()->create(['user_id' => $seller->id]);

        config()->set('payments.fake', true);

        $changeAddress = [
            'postal_code' => '891-2345',
            'address' => '香川県高松市小村町1-6-5',
            'building' => 'タウニー長田202',
        ];

        $this->withoutExceptionHandling();

        $this->actingAs($buyer)
            ->from(route('purchase.confirm', ['item' => $item->id]))
            ->followingRedirects()
            ->post(route('purchase.address.update', ['item' => $item->id]), $changeAddress)
            ->assertStatus(200);

        $this->post(route('payment.checkout', ['item' => $item->id]), [
            'payment_method' => 'card',
        ])->assertStatus(200);

        $purchase = Purchase::where('user_id', $buyer->id)
            ->where('item_id', $item->id)
            ->latest('id')
            ->first();

        $this->assertNotNull($purchase, 'Purchase was not created');

        $this->assertDatabaseHas('purchases', [
            'user_id' => $buyer->id,
            'item_id' => $item->id,
            'purchase_postal_code' => $changeAddress['postal_code'],
            'purchase_address' => $changeAddress['address'],
            'purchase_building' => $changeAddress['building'],
        ]);
    }
}
