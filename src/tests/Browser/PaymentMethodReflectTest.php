<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;
use App\Models\Item;

class PaymentMethodReflectTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function  test_selected_payment_method_reflected_in_confirmation_area()
    {
        $this->browse(function (Browser $browser) {
            $seller = User::factory()->create();
            $buyer = User::factory()->create();
            $item = Item::factory()->create(['user_id' => $seller->id]);

            $this->browse(function (Browser $browser) use ($buyer, $item) {
                $browser->loginAs($buyer)
                    ->visit(route('purchase.confirm', ['item' => $item->id]))
                    ->waitFor('#payment-select')

                    ->select('#payment-select', 'card')
                    ->pause(150)
                    ->assertSeeIn('#selected-payment-method', config('payments.methods.card', 'カード支払い'))
                    ->assertSelected('payment_method', 'card')

                    ->select('#payment-select', 'konbini')
                    ->pause(150)
                    ->assertSeeIn('#selected-payment-method', config('payments.methods.konbini', 'コンビニ払い'))
                    ->assertSelected('payment_method', 'konbini');
            });

           
        });
    }
}
