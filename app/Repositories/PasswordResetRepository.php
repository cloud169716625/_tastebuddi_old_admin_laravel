<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mails\ResetPassword;
use App\PasswordReset;
use App\User;
use DB;

class PasswordResetRepository
{
    public function errorResponse($error)
    {
        $user = [];
        return compact('user', 'error');
    }

    public function sendResetLinkEmail($email)
    {
        DB::beginTransaction();
        try{
            PasswordReset::where('email', '=', $email)->delete();

            $token = str_random(5);

            $password_reset = PasswordReset::create([
                'email' => $email,
                'token' => Hash::make($token),
            ]);
            
            $user = compact('email', 'token');

            Mail::to($email)->send(new ResetPassword($user));
            
            $user = compact('email');
            DB::commit();
            return compact('user');

        }catch(\Exception $e){

            DB::rollBack();
            return $this->errorResponse($e->getMessage());
        }
    }

    public function resetPassword($request)
    {
        DB::beginTransaction();
        try{
            $email = $request->get('email');
            $password_reset = PasswordReset::where('email', '=', $email)->first();

            if ($password_reset && Hash::check($request->get('token'), $password_reset->token)) {
                
                $user = User::where('email', '=', $email)->update([
                    'password' => Hash::make($request->get('password'))
                ]);

                PasswordReset::where('email', '=', $email)->delete();
                
                $user = compact('email');
                DB::commit();
                return compact('user'); 
            }

            return $this->errorResponse('Reset token not found');

        }catch(\Exception $e){

            DB::rollBack();
            return $this->errorResponse($e->getMessage());
        }
    }
}