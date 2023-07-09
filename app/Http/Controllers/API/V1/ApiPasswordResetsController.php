<?php

namespace App\Http\Controllers\API\V1;

use App\Repositories\PasswordResetRepository;
use App\Validators\ResetPasswordValidator;
use App\Validators\MailValidator;
use App\Models\Auth\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Class PasswordResetsController
 * @property PasswordReset passwordReset
 * @package App\Http\Controllers
 */
class ApiPasswordResetsController extends ApiBaseController
{
    /**
     *  Create a new controller instance.
     *
     */
    public function __construct()
    {
    }

    /**
     * @OA\Post(
     *      path="/password/email",
     *      tags={"Password"},
     *      summary="Send reset password email with token",
     *      security={},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="email",
     *                      description="Email Address",
     *                      type="string",
     *                 ),
     *             ),
     *         ),
     *     ),
     *      @OA\Response(
     *          response=422,
     *          description="Invalid Input"
     *     ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal Server Error"
     *     ),
     *      @OA\Response(
     *          response=200,
     *          description="Request OK"
     *     )
     *)
     */
    public function sendResetCodeEmail(Request $request)
    {
        $password_reset = new PasswordReset();

        if (!$password_reset->sendResetToken($request)) {
            return $this->apiErrorResponse(false, $password_reset->getErrors(true), self
                                            ::HTTP_STATUS_INVALID_INPUT, 'invalidInput');
        }

        return $this->apiSuccessResponse(
            compact('password_reset'),
            true,
            'Password reset code successfully sent to email'
        );
    }

    /**
     * @OA\Post(
     *      path="/password/reset",
     *      tags={"Password"},
     *      summary="Reset user's password",
     *      security={},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="email",
     *                      description="Email Address",
     *                      type="string",
     *                 ),
     *                  @OA\Property(
     *                      property="token",
     *                      description="Reset Token",
     *                      type="string",
     *                 ),
     *                  @OA\Property(
     *                      property="password",
     *                      description="Password",
     *                      type="string",
     *                 ),
     *                  @OA\Property(
     *                      property="password_confirmation",
     *                      description="Confirm Password",
     *                      type="string",
     *                 ),
     *             ),
     *         ),
     *     ),
     *      @OA\Response(
     *          response=422,
     *          description="Invalid Input"
     *     ),
     *      @OA\Response(
     *          response=404,
     *          description="Reset Token Not Found"
     *     ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal Server Error"
     *     ),
     *      @OA\Response(
     *          response=200,
     *          description="Request OK"
     *     )
     *)
     */
    public function resetPassword(Request $request)
    {
        $password_reset = PasswordReset::where('email', $request->email)->first();

        if (!$password_reset->resetPassword($request)) {
            return $this->apiErrorResponse(false, $password_reset->getErrors(true), self
                                            ::HTTP_STATUS_INVALID_INPUT, 'invalidInput');
        }
        return $this->apiSuccessResponse(compact('password_reset'), true, 'Password has been reset successfully!');
    }
}
