<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateItemFromLocationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'thumb_nail' => 'required',
            'category_id' => 'required|exists:categories,category_id',
            'item_name' => 'required',
            'price' => 'required|numeric'
        ];
    }
}
