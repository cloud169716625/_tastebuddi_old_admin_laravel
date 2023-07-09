<?php

namespace App\Repositories;

use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mails\VerifyEmail;
use App\User;
use JWTAuth;
use DB;

class UserRepository
{
    public function errorResponse($error)
    {
        $user = [];
        return compact('user', 'error');
    }

    public function create($request)
    {
        DB::beginTransaction();
        try {
            // check for nullable values
            $first_name = $request->get('first_name') ? $request->get('first_name') : null;
            $last_name = $request->get('last_name') ? $request->get('last_name') : null;
            $mobile_number = $request->get('mobile_number') ? $request->get('mobile_number') : null;

            // generate verify email token
            $verify_token = str_random(5);

            $user = User::create([
                'email' => $request->get('email'),
                'password' => Hash::make($request->get('password')),
                'first_name' => $first_name,
                'last_name' => $last_name,
                'mobile_number' => $mobile_number,
                'verify_token' => Hash::make($verify_token)
            ]);

            $token = JWTAuth::fromUser($user);

            Mail::to($request->get('email'))->send(new VerifyEmail($verify_token));

            DB::commit();
            return compact('user', 'token');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage());
        }
    }

    public function verifyEmail($request)
    {
        DB::beginTransaction();
        try {
            $user = User::where('email', '=', $request->get('email'))->first();

            if ($user && Hash::check($request->get('verify_token'), $user->verify_token)) {
                $user->email_verified_at = date('Y-m-d H:i:s');
                $user->verify_token = null;
                $user->save();

                DB::commit();
                return compact('user');
            }

            return $this->errorResponse('User not found');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage());
        }
    }

    public function resendEmail($email)
    {
        DB::beginTransaction();
        try {
            if ($user = User::where('email', '=', $email)->first()) {
                if ($user->email_verified_at) {
                    $error = 'Account is already verified';
                    return compact('user', 'error');
                } else {
                    $verify_token = str_random(5);
                    $user->verify_token = Hash::make($verify_token);
                    $user->save();

                    Mail::to($user->email)->send(new VerifyEmail($verify_token));
                }

                DB::commit();
                return compact('user');
            }

            return $this->errorResponse('User not found');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage());
        }
    }

    public function update($request)
    {
        DB::beginTransaction();
        try {
            $user = JWTAuth::toUser();

            if ($user && $user->id == $request->get('id')) {
                // check for nullable values
                $first_name = $request->get('first_name') ? $request->get('first_name') : $user->first_name;
                $last_name = $request->get('last_name') ? $request->get('last_name') : $user->last_name;
                $mobile_number = $request->get('mobile_number') ? $request->get('mobile_number') : $user->mobile_number;

                $user->first_name = $first_name;
                $user->last_name = $last_name;
                $user->mobile_number = $mobile_number;

                $user->save();
                DB::commit();
                return compact('user');
            }
            return $this->errorResponse('User not found');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage());
        }
    }

    public function uploadProfilePhoto($request)
    {
        DB::beginTransaction();
        try {
            $photo = $request->get('photo');
            $user  = JWTAuth::toUser();

            if ($user && $user->id == $request->get('id')) {
                $uniqname  = date('Ymd') . $user->id . '_' . uniqid() . '.png';
                $photo_url = url('storage/img/users' . $uniqname);
                $dir_path  = storage_path() . '/app/public/img/users';

                if (! is_dir($dir_path)) {
                    mkdir($dir_path, 755, true);
                }

                Image::make(file_get_contents($photo))
                        ->fit(200)
                        ->save($dir_path . $uniqname);

                $user->profile_photo_url = $uniqname;
                $user->save();

                DB::commit();
                return compact('user');
            }

            return $this->errorResponse('User not found');
        } catch (\Exception $e) {
            DB::rollBack();
            $profile_photo_url = [];
            $error = $e->getMessage();
            return compact('profile_photo_url', 'error');
        }
    }

    /** **/

}
