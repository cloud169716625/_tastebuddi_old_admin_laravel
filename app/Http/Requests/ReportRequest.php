<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReportRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'reason_id'     => ['bail', 'required', 'exists:report_categories,id'],
            'description'   => ['bail', 'required', 'string'],
            'attachments'   => ['bail', 'nullable', 'array'],
            'attachments.*' => ['image']
        ];
    }
}
