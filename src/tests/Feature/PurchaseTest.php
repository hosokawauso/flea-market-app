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



class PurchaseTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_user_can_complete_purchase_process()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $payment = Payment::factory()->create([
            'method' => 'コンビニ払い',
            'amount' => 1000, //明示的に追加
        ]);

        $purchase= [
            'postal_code' =>'760-0080',
            'address' => '香川県高松市サンポート2-1',
            'building' => 'マリンタイムプラザ30階',
            'payment_id' => $payment->id,
        ];

        $this->actingAs($user);

        Livewire::test(PurchasePage::class, ['item' => $item])
            ->set('paymentMethod', 'コンビニ払い')
            ->set('purchase', $purchase)
            ->call('purchase')
            ->assertRedirect('/mypage?page=buy');

        $this->assertDatabaseHas('purchases', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'payment_id' => $payment->id,
            'purchase_postal_code' => $purchase['postal_code'],
            'purchase_address' => $purchase['address'],
            'purchase_building' => $purchase['building'],
        ]);
    }

    public function test_sold_label_is_displayed_for_purchased_item_on_index_page()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $payment = Payment::factory()->create([
            'method' => 'コンビニ払い',
            'amount' => 1000,
        ]);

        $purchase = [
            'postal_code' =>'760-0080',
            'address' => '香川県高松市サンポート2-1',
            'building' => 'マリンタイムプラザ30階',
            'payment_id' => $payment->id,
        ];

        $this->actingAs($user);

        Livewire::test(PurchasePage::class, ['item' => $item])
            ->set('paymentMethod', 'コンビニ払い')
            ->set('purchase', $purchase)
            ->call('purchase')
            ->assertRedirect('/mypage?page=buy');

        $this->assertDatabaseHas('purchases', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'payment_id' => $payment->id,
            'purchase_postal_code' => $purchase['postal_code'],
            'purchase_address' => $purchase['address'],
            'purchase_building' => $purchase['building'],
        ]);

        $item->is_sold = true;
        $item->save();

        $response = $this->get("/");

        $response->assertSeeText($item->item_name);
        $response->assertSeeText('Sold');
    }

    public function test_purchased_item_is_displayed_on_my_page()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $payment = Payment::factory()->create([
            'method' => 'コンビニ払い',
            'amount' => 1000,
        ]);

        $purchase= [
            'postal_code' =>'760-0080',
            'address' => '香川県高松市サンポート2-1',
            'building' => 'マリンタイムプラザ30階',
            'payment_id' => $payment->id,
        ];

        $this->actingAs($user);

        Livewire::test(PurchasePage::class, ['item' => $item])
            ->set('paymentMethod', 'コンビニ払い')
            ->set('purchase', $purchase)
            ->call('purchase')
            ->assertRedirect('/mypage?page=buy');

        $response = $this->get('/mypage?page=buy');

        $response->assertStatus(200);
        $response->assertSeeText($item->item_name);
        $response->assertSee(asset('storage/' . $item->item_img));
    }
}