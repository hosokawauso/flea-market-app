<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Livewire\Livewire;
use App\Http\Livewire\PurchasePage;
use App\Models\User;
use App\Models\Item;
use App\Models\Payment;


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
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $changeAddress = [
            'postal_code' => '123-4567',
            'address' => '香川県高松市浜ノ町',
            'building' => 'サンポートタワー15階',
        ];

        $this->actingAs($user)
            ->withSession(['purchase_address' => $changeAddress]);

        Livewire::test(PUrchasePage::class, ['item' => $item])
            ->assertSee($changeAddress['postal_code'])
            ->assertSee($changeAddress['address'])
            ->assertSee($changeAddress['building']);
    }

    public function test_purchase_address_is_stored_with_purchased_item()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $payment = Payment::factory()->create([
            'method' => 'コンビニ払い',
            'amount' => 1000,
        ]);

        $changeAddress = [
            'postal_code' => '123-4567',
            'address' => '香川県高松市浜ノ町',
            'building' => 'サンポートタワー15階',
        ];

        $this->actingAs($user)
            ->withSession(['purchase_address' => $changeAddress]);

        Livewire::test(PurchasePage::class, ['item' => $item])
            ->set('paymentMethod', $payment->method)
            ->call('purchase')
            ->assertRedirect('/mypage?page=buy');


        $this->assertDatabaseHas('purchases', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'payment_id' => $payment->id,
            'purchase_postal_code' => $changeAddress['postal_code'],
            'purchase_address' => $changeAddress['address'],
            'purchase_building' => $changeAddress['building'],
        ]);
    }
}
