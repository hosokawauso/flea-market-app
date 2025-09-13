<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

class PaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'payment_method' => [
                'required', 
                Rule::in(array_keys(config('payments.methods'))),
            ],
        ];
    }

    public function messages()
    {
        return [
            'payment_method.required' => '支払い方法を選択してください',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $purchase = session('purchase_address') ?: [
                'postal_code' => Auth::user()->postal_code,
                'address' => Auth::user()->address,
                'building' => Auth::user()->building,
            ];

            $postal = (string)($purchase['postal_code'] ?? '');
            $address = (string)($purchase['address'] ?? '');

            if ($postal === '' || $address === '') {
                $validator->errors()->add(
                    'address', 
                    "配送先住所が入力されていません\n「変更する」から住所を入力してください"
                );
            }
        });
    }

    // デバッグ用
    protected function failedValidation(Validator $validator)
    {
        logger()->info('PurchaseRequest failed', [
            'errors'           => $validator->errors()->toArray(),
            'payload'          => $this->only('payment_method'),
            'session_purchase' => session('purchase_address'),
            'profile'          => [
                'postal_code' => Auth::user()->postal_code,
                'address'     => Auth::user()->address,
                'building'    => Auth::user()->building,
            ],
        ]);
        parent::failedValidation($validator);
    }
}