<?php

namespace App\Http\Controllers\API\V1;

use App\Enums\MediaCollectionType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReportRequest;
use App\Models\Users\Users;
use App\User;
use Illuminate\Http\Response;
use JWTAuth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ApiUsersController extends ApiBaseController
{
    /**
     * @OA\Get(
     *     path="/user",
     *     tags={"User"},
     *     operationId="show",
     *     security={{"BearerAuth":{}}},
     *     summary="Retrieve user's information",
     *     @OA\Response(response=400, description="Invalid Token"),
     *     @OA\Response(response=401, description="Token Expired"),
     *     @OA\Response(response=404, description="User Not Found / Token Not Found"),
     *     @OA\Response(response=422, description="Invalid Input"),
     *     @OA\Response(response=500, description="Internal Server Error"),
     *     @OA\Response(response=200, description="Request OK")
     *
     *)
     */
    public function show(Request $request)
    {
        try {
            $user = JWTAuth::toUser();
        } catch (\Exception $e) {
            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }

        return $this->apiSuccessResponse(compact('user'), true, 'User Details', 200);
    }

    /**
     * @OA\Put(
     *      path="/user",
     *      tags={"User"},
     *      operationId="update",
     *      summary="Update user's information",
     *      security={{"BearerAuth":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="User ID",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="integer",
     *         ),
     *     ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="first_name",
     *                      description="First Name",
     *                      type="string",
     *                      example="John"
     *                 ),
     *                  @OA\Property(
     *                      property="last_name",
     *                      description="Last Name",
     *                      type="string",
     *                      example="Doe"
     *                 ),
     *                  @OA\Property(
     *                      property="country",
     *                      description="Country",
     *                      type="string",
     *                      example="Philippines"
     *                 ),
     *                  @OA\Property(
     *                      property="mobile_number",
     *                      description="Mobile Number",
     *                      type="string",
     *                      example="+63 9277468888"
     *                 ),
     *                  @OA\Property(
     *                      property="address",
     *                      description="Address",
     *                      type="string",
     *                      example="216 Cabreros Street Cebu City"
     *                 ),
     *                  @OA\Property(
     *                      property="password",
     *                      description="Password",
     *                      type="string",
     *                      example="password"
     *                 ),
     *                  @OA\Property(
     *                      property="is_allowed",
     *                      description="If the user is allowed to post prices",
     *                      type="integer",
     *                      example="1"
     *                 ),
     *             ),
     *         ),
     *     ),
     *      @OA\Response(response=400, description="Invalid Token"),
     *      @OA\Response(response=401, description="Token Expired"),
     *      @OA\Response(response=404, description="User Not Found / Token Not Found"),
     *      @OA\Response(response=422, description="Invalid Input"),
     *      @OA\Response(response=500, description="Internal Server Error"),
     *      @OA\Response(response=200, description="Request OK")
     *
     *)
     */
    public function update(Request $request)
    {
        if (! $user  = JWTAuth::toUser()) {
            return $this->apiErrorResponse(false, 'Invalid JWT Token', 400, 'invalidToken');
        }

        if (! $user->store($request)) {
            return $this->apiErrorResponse(false, $user->getErrors(true), 400, 'savingError');
        }


        return $this->apiSuccessResponse(['user' => $user ], true, 'User successfully updated');
    }

    /**
     * @OA\Post(
     *      path="/user/photo",
     *      tags={"User"},
     *      summary="Upload profile photo",
     *      security={{"BearerAuth":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="photo",
     *                      description="Image File",
     *                      format="file",
     *                      type="string",
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(response=400, description="Invalid Token"),
     *     @OA\Response(response=401, description="Token Expired"),
     *     @OA\Response(response=404, description="User Not Found / Token Not Found"),
     *     @OA\Response(response=422, description="Invalid Input"),
     *     @OA\Response(response=500, description="Internal Server Error"),
     *     @OA\Response(response=200, description="Request OK")
     *)
     */
    public function uploadProfilePhoto(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'photo' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->apiErrorResponse(false, $validator->errors(), self::HTTP_STATUS_INVALID_INPUT, 'invalidInput');
        }

        $user = Users::find(JWTAuth::toUser()->id);

        /** @var \Illuminate\Http\Testing\File */
        $image = $request->photo;

        $name = Str::random(30);
        $hashName = explode('.', $image->hashName())[0];

        $user->addMediaFromRequest('photo')
            ->usingName("{$name}{$hashName}")
            ->usingFileName($image->hashName())
            ->toMediaCollection(MediaCollectionType::USER_AVATAR);

        return $this->apiSuccessResponse(
            ['user'=> $user->fresh() ],
            true,
            'Profile Photo Uploaded Successfully ',
            self::HTTP_STATUS_REQUEST_OK
        );
    }

    /**
     * Report User.
     */
    public function report(ReportRequest $request, Users $user)
    {
        if (JWTAuth::toUser()->id == $user->id) {
            return $this->apiErrorResponse(
                false,
                'You cannot report your own account!',
                self::HTTP_STATUS_INVALID_INPUT,
                'invalidInput'
            );
        }

        $user->report(
            $request->input('reason_id'),
            $request->input('description'),
            $request->file('attachments', [])
        );

        return response()->json([], Response::HTTP_OK);
    }
}
