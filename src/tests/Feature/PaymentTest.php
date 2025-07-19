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


class PaymentTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_selected_payment_method_reflected_in_confirmation_area()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $payment = Payment::factory()->create([
            'method' => 'カード支払い',
            'amount' => 1000,
        ]);

        $this->actingAs($user);

        Livewire::test(\App\Http\Livewire\PurchasePage::class, ['item' => $item])
        ->set('paymentMethod', 'カード支払い')
        ->assertSet('paymentMethod', 'カード支払い')
        ->assertSee('カード支払い');
    }
}
