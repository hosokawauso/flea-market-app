<?php

namespace App\Http\Livewire;

use App\Models\Item;
use App\Models\User;
use App\Models\Purchase;
use App\Models\Payment;
use App\Http\Requests\AddressRequest;
use App\Http\Requests\PurchaseRequest;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class PurchasePage extends Component
{
    public Item $item;
    public $user;

    /* public $selectedMethod = ''; */
    public $paymentMethod = '';

    public $purchase = [
        'postal_code' => '',
        'address' => '',
        'building' => '',
    ];

    public bool $changeAddress = false;

    public function mount(Item $item): void
    {
        $this->item = $item;
        $this->user =Auth::user();

        $this->purchase = session('purchase_address', [
            'postal_code' => $this->user->postal_code,
            'address' => $this->user->address,
            'building' => $this->user->building,
        ]);
    }

/*     public function refreshAddress()
    {
    $this->purchase = session('purchase_address', [
        'postal_code' => $this->user->postal_code,
        'address' => $this->user->address,
        'building' => $this->user->building,
        ]);
     } */



    public function getPaymentLabelProperty(): string
    {
        return $this->paymentMethod ?: '未選択';        

    }

/*     public function getSelectedPaymentMethodLabelProperty(): string
    {
        $payment = Payment::find($this->paymentMethod);
        return $payment ? $payment->method : '未選択';
    }
 */
    public function toggleAddress(): void
    {
        $this->changeAddress = !$this->changeAddress;
    }

    public function rules()
    {
        return [
            'paymentMethod' => ['required'],
            'purchase.postal_code' => ['required', 'regex:/^\d{3}-\d{4}$/'],
            'purchase.address' => ['required'],
            'purchase.building' => ['nullable'],
        ];
    }

    public function messages()
    {
        return [
            'paymentMethod.required' => '支払い方法を選択してください',
            'purchase.postal_code.required' => '郵便番号を入力してください',
            'purchase.postal_code.regex' => 'ハイフン(-)を含めた8文字で入力してください',
            'purchase.address.required' => '住所を入力してください',

        ];
    }
    
    public function purchase()
    {
        $this->validate();

        
        $purchase = session('purchase_address') ?? [
            'postal_code' => Auth::user()->postal_code,
            'address' => Auth::user()->address,
            'building' => Auth::user()->building,
        ];

        $payment = Payment::where('method', $this->paymentMethod)->first();
       
        
        Purchase::create([
            'user_id' => Auth::id(),
            'item_id' => $this->item->id,
            'payment_id' =>$payment->id,
            'purchase_postal_code' => $purchase['postal_code'],
            'purchase_address' => $purchase['address'],
            'purchase_building' => $purchase['building'],
        ]);

        /* Item::where('id', $this->item->id)->update(['is_sold' => true]); */

        $item = Item::find($this->item->id);
        $item->is_sold = true;
        $item->save();

        session()->forget('purchase_address');

        return redirect('/mypage');
    }

    public function render()
    {
        $payments = Payment::all();
        return view('livewire.purchase-page', compact('payments'));
    }

}