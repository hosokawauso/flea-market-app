<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CheckoutRequest extends FormRequest
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

    protected function prepareForValidation()
    {
        if ($this->has('payment_method')) {
            $this->merge([
                'payment_method' => strtolower(trim((string)$this->input('payment_method'))),
            ]);
        }
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

        public function messages(): array
    {
        return [
            'payment_method.required' => '支払い方法を選択してください',
        ];
    }
}
