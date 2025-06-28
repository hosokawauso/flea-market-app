<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
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
            'item_name' => ['required'],
            'description' => ['required','max:255'],
            'item_img' => ['required','file', 'mimes:jpeg,png' ],
            'category' => ['required'],
            'condition' => ['required'],
            'price' => ['required', 'integer', 'min:0'],
        ];
    }

    public function messages()
    {
        return [
            'item_name.required' => '商品名を入力してください',
            'description.required' => '商品説明を入力してください',
            'description.max' => '商品説明は255文字以内で入力してください',
            'item_img.required' => '商品画像を選択してください',
            'item_img.mimes' => '拡張子は .jpeg または .png の画像を選択してください',
            'category.required' => '商品のカテゴリーを選択してください',
            'condition.required' => '商品の状態を選択してください',
            'price.required' => '商品価格を設定してください',
            'price.integer' => '商品価格は整数で設定してください',
            'price.min' => '商品価格は０円以上で設定してください',
        ];
    }
}
