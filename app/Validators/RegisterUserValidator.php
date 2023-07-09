<?php

namespace App\Validators;

class RegisterUserValidator
{
	public static function rules()
	{
		return [
            'email'         => 'required|string|email|max:50|unique:users',
            'password'      => 'required|string|min:8|max:24|regex:/[a-z]/|regex:/[A-Z]/',
            'first_name'    => 'required|string',
            'last_name'     => 'required|string',
            'mobile_number' => 'nullable|string'
        ];
	}

	public static function messages()
	{
		return [
            'email.required'     => 'Please fill in your e-mail address.',
            'email.max'          => 'E-mail address may not be greater than 50 characters.',
            'email.email'        => 'This e-mail address format is invalid.',
            'email.unique'       => 'This e-mail has been used already for registration.',

            'password.required'  => 'Please fill in your password.',
            'password.max'       => 'Password may not be greater than 24 characters',
            'password.min'       => 'Password must be at least 8 characters',
            'password.regex'     => 'Password should contain both uppercase and lowercase characters.',

            'first_name.required' => 'Please fill in your first name.',
            'last_name.required' => 'Please fill in your last name.',
        ];
	}

}
