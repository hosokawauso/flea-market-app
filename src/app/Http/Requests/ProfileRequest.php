<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
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
            'profile_img' => ['file', 'mimes:jpeg,png', 'max:2048', 'nullable'],
            'name' => ['required'],
            'postal_code' => ['required', 'regex:/^\d{3}-\d{4}$/'],
            'address' => ['required'],
            'building' => ['nullable'],
        ];
    }

    public function messages()
    {
        return [
            'profile_img.mimes' => '拡張子は .jpeg または .png の画像を選択してください',
            'name.required' => 'ユーザー名を入力してください',
            'postal_code.required' => '郵便番号を入力してください',
            'postal_code.regex' => 'ハイフン(-)を含めた8文字で入力してください',
            'address.required' => '住所を入力してください',

        ];
    }
}
