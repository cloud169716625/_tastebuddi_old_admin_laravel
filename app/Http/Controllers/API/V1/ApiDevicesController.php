<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;

class ApiDevicesController extends ApiBaseController
{

    /**
     * @OA\Post(
     *      path="/device/register",
     *      tags={"Devices"},
     *      summary="Register a Device",
     *      security={{"BearerAuth":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="device_token",
     *                      description="Device Token",
     *                      type="string",
     *                      example="f0V5aAjGW7I:APA91bGlshinKXIAWR97SotBcihmmZ5ZxhZeZ5z-IXIZX1E64eVl3KOmKa8dMnGdHuwpXtfsvMtW5l03JZUmWWcoB2PfOZB1OZ58LqoMZa83Ih9B20i5Flu9wpAAP9mcBAwHCsnZwXvl"
     *                 ),
     *                  @OA\Property(
     *                      property="type",
     *                      description="Device Type",
     *                      type="string",
     *                      enum="android|ios",
     *                      example="ios"
     *                 ),
     *             ),
     *         ),
     *     ),
     *      @OA\Response(response=400, description="Bad Request"),
     *      @OA\Response(response=401, description="Token Expired"),
     *      @OA\Response(response=404, description="Device Not Found"),
     *      @OA\Response(response=409, description="Device is already registered"),
     *      @OA\Response(response=500, description="Internal Server Error"),
     *      @OA\Response(response=200, description="Device Added")
     *)
     */
    public function store(Request $request)
    {
        $this->authenticate($request);
        $user  = JWTAuth::toUser();

        $validator = Validator::make($request->all(), [
            'device_token' => 'required',
            'type' => 'required|in:android,ios'
        ]);

        if ($validator->fails()) {
            $message = [];
            foreach ($validator->errors()->toArray() as $key => $value) {
                $message[$key] = $value[0];
            }
            $message = implode(", ", $message);
            return $this->apiErrorResponse(
                false,
                $message,
                self::HTTP_STATUS_INVALID_INPUT,
                'invalidFields'
            );
        }

        DB::beginTransaction();

        try {
            $device = Device::where('device_token', $request->device_token)->first();

            if ($device) {
                DB::rollBack();

                return $this->apiErrorResponse(
                    false,
                    'Device is already registered.',
                    self::HTTP_STATUS_CODE_CONFLICT,
                    'invalidFields'
                );
            }

            $device = new Device();
            $device->user_id = $user->id;
            $device->device_token = $request->device_token;
            $device->type = $request->type;
            $device->save();

            DB::commit();

            return $this->apiSuccessResponse(
                ['device' => $device ],
                true,
                'Device added.'
            );
        } catch (\Illuminate\Database\QueryException $exception) {
            DB::rollBack();

            return ['msg' => $exception->getMessage()];

            return $this->apiErrorResponse(
                false,
                'Device not registered.',
                self::HTTP_STATUS_BAD_REQUEST,
                'badRequest'
            );
        } catch (\Exception $exception) {
            DB::rollBack();

            return $this->apiErrorResponse(
                false,
                'Internal server error.',
                self::INTERNAL_SERVER_ERROR,
                'internalServerError'
            );
        }
    }

    /**
     * @OA\Delete(
     *      path="/device/unregister/{device_id}",
     *      tags={"Devices"},
     *      summary="Register a Device",
     *      security={{"BearerAuth":{}}},
     *      @OA\Parameter(
     *          description="Device ID",
     *          in="path",
     *          name="device_id",
     *          required=true,
     *          @OA\Schema(
     *              type="integer",
     *         ),
     *     ),
     *      @OA\Response(response=400, description="Bad Request"),
     *      @OA\Response(response=401, description="Token Expired"),
     *      @OA\Response(response=404, description="Device Not Found"),
     *      @OA\Response(response=409, description="Device is already registered"),
     *      @OA\Response(response=500, description="Internal Server Error"),
     *      @OA\Response(response=200, description="Device Deleted")
     *)
     */
    public function destroy(Request $request, $device_id)
    {
        $this->authenticate($request);
        $user  = JWTAuth::toUser();

        DB::beginTransaction();

        try {
            $device = Device::where('device_id', $device_id)->where('user_id', $user->id)->first();

            if (!$device) {
                DB::rollBack();

                return $this->apiErrorResponse(
                    false,
                    'Device not found.',
                    self::HTTP_STATUS_NOT_FOUND,
                    'notFound'
                );
            }

            $device->delete();

            DB::commit();

            return $this->apiSuccessResponse(
                ['device' => $device],
                true,
                'Device deleted.'
            );
        } catch (\Illuminate\Database\QueryException $exception) {
            DB::rollBack();

            return $this->apiErrorResponse(
                false,
                'Device not registered.',
                self::HTTP_STATUS_BAD_REQUEST,
                'badRequest'
            );
        } catch (\Exception $exception) {
            DB::rollBack();

            return $this->apiErrorResponse(
                false,
                'Internal server error.',
                self::INTERNAL_SERVER_ERROR,
                'internalServerError'
            );
        }
    }

    public function test(Request $request)
    {
        $this->authenticate($request);
        $user = JWTAuth::toUser();


        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60*20);
        $optionBuilder->setContentAvailable(true);


        $notificationBuilder = new PayloadNotificationBuilder('Test Title');
        $notificationBuilder->setBody("Test Body")
            ->setSound('default');

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData(['a_data' => 'my_data']);

        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

//        $token = $token ? $token : "f0V5aAjGW7I:APA91bGlshinKXIAWR97SotBcihmmZ5ZxhZeZ5z-IXIZX1E64eVl3KOmKa8dMnGdHuwpXtfsvMtW5l03JZUmWWcoB2PfOZB1OZ58LqoMZa83Ih9B20i5Flu9wpAAP9mcBAwHCsnZwXvl";

//        $token = "dZrcldLvtDQ:APA91bHlaJUATmGfmQc38dKQD7PjmpWcr89Ohef0N5YVE2vAHQyrrB8FBn2mfdhaT2kkHeiz35sQRUJfpgAjB8M6GnJT7TI9ZdmSZm_r-3z7x99xZsljGcrWQrzpenlLaWQTpymqall4";

        $token = "d4SYZGtglJ4:APA91bEellJZX5qR-daJamA5B1PYkEvvG0L4q04ja9crwDVlnS
                    Pn2DUt42zQFse3O0oigRpPuWPXhoHDxLRWHTmnbKJUSTEHgo8hMmetxqhR9oDZTQvvIOustq-Ea8tOMswFl5sLaFd4";


        $downstreamResponse = FCM::sendTo($token, $option, $notification, $data);



        // multi-device
//        $downstreamResponse = FCM::sendTo($tokens, $option, $notification, $data);

        $downstreamResponse->numberSuccess();
        $downstreamResponse->numberFailure();
        $downstreamResponse->numberModification();

        // return Array - you must remove all this tokens in your database
        $downstreamResponse->tokensToDelete();

        // return Array (key : oldToken, value : new token - you must change the token in your database)
        $downstreamResponse->tokensToModify();

        // return Array - you should try to resend the message to the tokens in the array
        $downstreamResponse->tokensToRetry();

        // return Array (key:token, value:error)
        // - in production you should remove from your database the tokens present in this array
        $downstreamResponse->tokensWithError();

        return [
            'user' => $user,
            'device' =>Device::where('device_token', $token)->first()
        ];
    }
}
