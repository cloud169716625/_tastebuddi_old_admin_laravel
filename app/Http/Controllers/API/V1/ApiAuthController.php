<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Users\Users;
use App\Repositories\UserRepository;
use App\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use JWTAuth;
use App\Validators\RegisterUserValidator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ApiAuthController extends ApiBaseController
{
    protected $userRepo;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    /**
     *
     * @OA\Post(
     *      path="/auth/login",
     *      tags={"Auth"},
     *      summary="Login user. Authenticate credentials and returns a token.",
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
     *                      example="testuser@gmail.com"
     *                 ),
     *                  @OA\Property(
     *                      property="password",
     *                      description="Password",
     *                      type="string",
     *                      example="password"
     *                 ),
     *             ),
     *         ),
     *     ),
     *      @OA\Response(
     *          response=400,
     *          description="Invalid Credentials"
     *     ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal Server Error"
     *     ),
     *      @OA\Response(
     *          response=200,
     *          description="Login Success"
     *     )
     *)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return $this->apiErrorResponse(false, 'Invalid Credentials', self::HTTP_STATUS_BAD_REQUEST, 'invalidCredentials');
            }
        } catch (JWTException $e) {
            return $this->apiErrorResponse(false, 'Could Not Create Token '.$e->getMessage(), self::HTTP_STATUS_NOT_FOUND, 'tokenAbsent');
        } catch (\Exception $e) {
            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }

        $user = JWTAuth::user();

        if ($user->disabled_at) {
            return $this->apiErrorResponse(false, 'Your account has been disabled.', self::HTTP_STATUS_BAD_REQUEST, 'accoutDisabled');
        }

        return $this->apiSuccessResponse(compact('user', 'token'), true, 'Login Success', self::HTTP_STATUS_REQUEST_OK);
    }

    /**
     * @OA\Post(
     *      path="/auth/register",
     *      tags={"Auth"},
     *      summary="Register user. Creates new user, sends 'thank you' email and returns a token.",
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
     *                      example="testuser@gmail.com"
     *                 ),
     *                  @OA\Property(
     *                      property="password",
     *                      description="Password",
     *                      type="string",
     *                      example="password",
     *                 ),
     *                  @OA\Property(
     *                      property="first_name",
     *                      description="First Name",
     *                      type="string",
     *                      example="Jane"
     *                 ),
     *                  @OA\Property(
     *                      property="last_name",
     *                      description="Last Name",
     *                      type="string",
     *                      example="Doe"
     *                 ),
     *                  @OA\Property(
     *                      property="mobile_number",
     *                      description="Mobile Number",
     *                      type="string",
     *                      example="+61 412345678"
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
     *          response=201,
     *          description="Request Created"
     *     )
     *)
     */
    public function register(Request $request)
    {
        $validator = \Validator::make($request->all(), RegisterUserValidator::rules(), RegisterUserValidator::messages());

        if ($validator->fails()) {
            return $this->apiErrorResponse(false, $validator->errors()->first(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }

        $user =  new Users();
        try {
            if (!$user->store($request)) {
                return $this->apiErrorResponse(false, $user->getErrors(), self::INTERNAL_SERVER_ERROR, 'sqlError');
            }
        } catch (\Exception $e) {
            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }

        $token = $user->getJwtToken();

        return $this->apiSuccessResponse(compact('user', 'token'), true, 'User has been
                                registered successfully!', self::HTTP_STATUS_REQUEST_OK);
    }

    /**
     * @OA\Post(
     *      path="/auth/email/check",
     *      tags={"Auth"},
     *      summary="Check email if exists. Used as a first step in user registration",
     *      description="",
     *      operationId="",
     *      @OA\Parameter(
     *          in="query",
     *          name="email",
     *          description="",
     *          required=true,
     *          @OA\Schema(
     *              type="string",
     *         ),
     *     ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *     )
     *)
     */
    public function checkEmail(Request $request)
    {
        if (!filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            return $this->apiErrorResponse(false, 'Invalid Email Format', 400, 'invalidEmailFormat');
        }

        if ($user   = (new User())->emailExists($request->email)) {
            return $this->apiSuccessResponse(['email_exists'  =>  true  ], true, 'Email exists', 200);
        }

        return $this->apiSuccessResponse(['email_exists'    =>  false  ], true, 'Email does not exists', 200);
    }

    /**
     * @OA\Get(
     *      path="/auth/user",
     *      tags={"Auth"},
     *      summary="Get authenticated user's information",
     *      security={{"BearerAuth":{}}},
     *      @OA\Response(
     *          response=400,
     *          description="Invalid Token"
     *     ),
     *      @OA\Response(
     *          response=401,
     *          description="Token Expired"
     *     ),
     *      @OA\Response(
     *          response=404,
     *          description="Token Not Found"
     *     ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal Server Error"
     *     ),
     *      @OA\Response(
     *          response=200,
     *          description="Authenticated User"
     *     )
     *)
     */
    public function getAuthUser()
    {
        try {
            $user = JWTAuth::toUser();
        } catch (\Exception $e) {
            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }

        return $this->apiSuccessResponse($user, true, 'Authenticated User', 200);
    }

    /**
     * @OA\Post(
     *      path="/auth/verify",
     *      tags={"Auth"},
     *      summary="Verify a user after registration ",
     *      description="A 6 character verification code will be sent through email",
     *      operationId="",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="uid",
     *                      description="<b>Required</b> User ID",
     *                      type="string",
     *                      example="141203"
     *                 ),
     *                  @OA\Property(
     *                      property="verification_code",
     *                      description="<b>Required</b> Verification Code",
     *                      type="string",
     *                      example="",
     *                 ),
     *             ),
     *         ),
     *     ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *     )
     *)
     */
    public function verify(Request $request)
    {
        // Another option of using raw $request->user_id is to convert the user id using \Helpers\Utils::convertInt().
        // Emails can also be used instead of user id if the emails are unique and possibly used as usernames.
        $user = User::find($request->uid);

        if (!$user) {
            return $this->apiErrorResponse(false, 'Invalid User ID', 400, 'invalidUserId');
        }

        if (!$user->verify($request->verification_code)) {
            return $this->apiErrorResponse(false, $user->getErrors(true), 400, 'verificationError');
        }

        $token = JWTAuth::fromUser($user);

        return $this->apiSuccessResponse(compact('user', 'token'), true, 'Account Verified', 200);
    }


    /**
     * @OA\Post(
     *      path="/auth/verification/resend",
     *      tags={"Auth"},
     *      summary="Resend verification code",
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
     *          response=400,
     *          description="Invalid Token / Bad Request"
     *     ),
     *      @OA\Response(
     *          response=401,
     *          description="Token Expired"
     *     ),
     *      @OA\Response(
     *          response=404,
     *          description="User Not Found / Token Not Found"
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
    public function resendVerificationCode(Request $request)
    {
        if (!filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            return $this->apiErrorResponse(false, 'Invalid Email', self::HTTP_STATUS_INVALID_INPUT, 'invalidEmail');
        }

        // On instances that emails are not used as usernames or identifiers then
        // you may also use user id for identification

        $user = (new Users())->emailExists($request->email);

        if (!$user) {
            return $this->apiErrorResponse(false, 'Email does not exists', self::HTTP_STATUS_INVALID_INPUT, 'invalidInput');
        }

        if ($user->is_verified) {
            return $this->apiErrorResponse(false, 'User already verified', self::HTTP_STATUS_INVALID_INPUT, 'userAlreadyVerified');
        }

        if (!$user->resendVerificationCode()) {
            return $this->apiErrorResponse(false, $user->getErrors(true), self::HTTP_STATUS_INVALID_INPUT, 'verificationCodeError');
        }

        return $this->apiSuccessResponse([], true, 'Verification code successfully sent to email', self::HTTP_STATUS_REQUEST_OK);
    }

    /**
     * @OA\Get(
     *      path="/auth/logout",
     *      tags={"Auth"},
     *      summary="Logout user",
     *      security={{"BearerAuth":{}}},
     *      @OA\Response(
     *          response=400,
     *          description="Invalid Token"
     *     ),
     *      @OA\Response(
     *          response=401,
     *          description="Token Expired"
     *     ),
     *      @OA\Response(
     *          response=404,
     *          description="Token Not Found"
     *     ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal Server Error"
     *     ),
     *      @OA\Response(
     *          response=200,
     *          description="Logout Success"
     *     )
     *)
     */
    public function logout()
    {
        try {
            $user = JWTAuth::invalidate();
        } catch (\Exception $e) {
            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }

        return $this->apiSuccessResponse([], true, 'Logged out successfully!', self::HTTP_STATUS_REQUEST_OK);
    }

    /**
     * Delete My Account
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function deleteAccount(Request $request)
    {
        $user = $request->user();

        if ($user) {
            DB::transaction(function () use ($user) {
                $user->update(['email' => $user->email.uniqid('_deleted_')]);

                $user->delete();
            });
        }

        JWTAuth::invalidate();

        return $this->apiSuccessResponse([]);
    }

    /**
     * Verify Account
     */
    public function passwordVerification(Request $request)
    {
        $request->validate([
            'password' => 'required'
        ]);

        if (Hash::check($request->password, $request->user()->password)) {
            return $this->apiSuccessResponse([
                'token' => JWTAuth::fromUser($request->user())
            ]);
        }

        return $this->apiErrorResponse(false, 'Invalid Credentials', self::HTTP_STATUS_BAD_REQUEST, 'invalidCredentials');
    }
}
