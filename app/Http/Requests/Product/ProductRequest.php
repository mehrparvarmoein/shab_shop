<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->isMethod('post') || $this->route('product')->user_id == auth()->id();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'title'          => 'required|string',
            'price'          => 'required|numeric|gt:0',
            'shipping_price' => 'required|numeric|gte:0',
            'images'         => 'nullable|array',
            'images.*'       => 'file|max:5120|mimes:jpg,jpeg,png,gif,bmp,svg,webp',
        ];
    }
}
