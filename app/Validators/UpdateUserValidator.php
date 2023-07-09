<?php

namespace App\Validators;

class UpdateUserValidator
{
	public static function rules()
	{
		return [
            'first_name'    => 'nullable|string',
            'last_name'     => 'nullable|string',
			'mobile_number' => 'nullable|string',
			'email'       =>   'nullable|string',
        ];
	}

	public static function messages()
	{
		return [

        ];
	}

}