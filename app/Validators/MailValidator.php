<?php

namespace App\Validators;

class MailValidator
{
	public static function rules()
	{
		return [
            'email' => 'required|string|email|exists:users'
        ];
	}

	public static function messages()
	{
		return [
            'email.required'     => 'Please fill in your e-mail address.',
            'email.email'        => 'Email address format is invalid.',
            'email.exists'       => 'This email address was not registered in our system.',
        ];
	}
	
}