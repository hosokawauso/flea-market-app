<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;


class PaymentTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_selected_payment_method_reflected_in_confirmation_area()
    {
        $seller = User::factory()->create();
        $buyer  = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $seller->id]);

        $this->withSession([
            'purchase_address' => [
                'postal_code' => '123-456',
                'address' => '香川県高松市浜ノ町',
                'building' => 'マリンタワー1120',
                ]
            ]);

        $this->actingAs($buyer)
            ->withSession([
                'purchase_address' => [
                'postal_code' => '123-456',
                'address' => '香川県高松市浜ノ町',
                'building' => 'マリンタワー1120',
                ],
                '_old_input' => [
                    'payment_method' => 'konbini'
                ],
            ])

            ->get(route('purchase.confirm', ['item' => $item->id]))
            ->assertStatus(200)
            ->assertSee(
                config('payments.methods.konbini', 'コンビニ払い')
            );

    }
}
