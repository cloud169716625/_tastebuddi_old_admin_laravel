<?php

namespace App\Validators;

class ResetPasswordValidator
{
	public static function rules()
	{
		return [
            'email' => 'required|string|email|exists:users',
            'password' => 'required|string|min:8|max:24|regex:/[a-z]/|regex:/[A-Z]/'
        ];
	}

	public static function messages()
	{
		return [
            'email.required'     => 'Please fill in your e-mail address.',
            'email.email'        => 'Email address format is invalid.',
            'email.exists'       => 'This email address was not registered in our system.',

            'password.required'  => 'Please fill in your password.',
            'password.max'       => 'Password may not be greater than 50 characters',
            'password.min'       => 'Password must be at least 6 characters',
            'password.regex'     => 'Password should contain both uppercase and lowercase characters.',
        ];
	}

}
