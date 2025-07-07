<?php

namespace App\Http\Livewire;

use App\Models\Item;
use App\Models\User;
use Livewire\Component;

class PaymentMethod extends Component
{
    public Item $item;
    public User $user;

    public $selectedMethod = '';
    public $paymentMethod = '';

    public $changeAddress = false;
    public $postal_code;
    public $address;
    public $building;

    public function mount(Item $item, User $user): void
    {
        $this->item = $item;
        $this->user = $user;

        $this->postal_code = $this->user->postal_code;
        $this->address = $this->user->address;
        $this->building = $this->user->building;
    }

    public function render()
    {
        return view('livewire.payment-method');
    }

    public function getPaymentLabelProperty(): string
    {
        return [
            1 => 'コンビニ払い',
            2 => 'カード支払い',
        ][$this->paymentMethod] ?? '未選択';

        return $labels[$this->paymentMethod] ?? '未選択';
    }

    public function toggleAddress()
    {
        $this->changeAddress = !$this->changeAddress;
    }

    public function purchase()
    {
        session()->flash('success', '購入が完了しました');
        return redirect('/mypage');
    }
}