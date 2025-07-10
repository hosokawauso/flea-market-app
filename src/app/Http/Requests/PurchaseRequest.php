<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
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
            'paymentMethod' => ['required'],
            'purchase_postal_code' => ['required', 'regex:/^\d{3}-\d{4}$/'],
            'purchase_address' => ['required'],
            'purchase_building' => ['nullable'],
        ];
    }

    public function messages()
    {
        return [
            'paymentMethod.required' => '支払い方法を選択してください',
            'purchase_postal_code.required' => '郵便番号を入力してください',
            'purchase_postal_code.regex' => 'ハイフン(-)を含めた8文字で入力してください',
            'purchase_address.required' => '住所を入力してください',

        ];
    }
}
