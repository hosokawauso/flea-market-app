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
            'profile_img' => [
                'file',
                'mimes:jpeg,png',
                'max:2048',
                'nullable',
            ],
        ];
    }

    public function message()
    {
        return [
            'profile_img.mines' => '拡張子は .jpeg または .png の画像を選択してください'
        ];
    }
}
