<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateItemToVerifiedBusinessRequest extends FormRequest
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
            'thumb_nail' => 'required',
            'category_id' => 'required|exists:categories,category_id',
            'item_name' => 'required',
            'price' => 'required|numeric'
        ];
    }
}
