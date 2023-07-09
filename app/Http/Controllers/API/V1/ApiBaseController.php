<?php
/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="TravelBuddi API",
 *      description="A product of Appetiser",
 *      @OA\Contact(
 *          name="Iazell",
 *          email="iazell.raper@appetiser.com.au"
 *     )
 *)
 */

/**
 *  @OA\Server(
 *      url="{ip or domain}/api/v1",
 *      description="Base Endpoint"
 *)
 */

/**
 * @OA\SecurityScheme(
 *      securityScheme="BearerAuth",
 *      type="http",
 *      scheme="bearer"
 *)
 */

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use JWTAuth;

class ApiBaseController extends Controller
{
    public const HTTP_STATUS_REQUEST_OK    = 200;


    /*
    |--------------------------------------------------------------------------
    | Client Error
    |--------------------------------------------------------------------------
    |
    | The request contains bad syntax or cannot be fulfilled.
    |
    */

    public const HTTP_STATUS_BAD_REQUEST             = 400;
    public const HTTP_STATUS_MESSAGE_BAD_REQUEST     = 'The request could not be understood 
                                                        by the server due to malformed syntax.';
    public const HTTP_STATUS_UNAUTHORIZED            = 401;
    public const HTTP_STATUS_MESSAGE_UNAUTHORIZED    = 'The request requires user authentication.';
    public const HTTP_STATUS_CODE_FORBIDDEN          = 403;
    public const HTTP_STATUS_MESSAGE_FORBIDDEN       = 'The server understood the request, but is refusing to fulfill it.';
    public const HTTP_STATUS_NOT_FOUND               = 404;
    public const HTTP_STATUS_MESSAGE_NOT_FOUND       = 'There is no resource behind the URI.';
    public const HTTP_STATUS_CODE_CONFLICT           = 409;
    public const HTTP_STATUS_MESSAGE_CONFLICT        = 'The request could not be completed due to a conflict 
                                                        with the current state of the target resource.';
    public const HTTP_STATUS_INVALID_INPUT           = 422;
    public const HTTP_STATUS_MESSAGE_INVALID_INPUT   = 'The server cannot process the entity, if a file cannot be 
                                                        formatted or mandatory fields are missing in the payload.';

    /*
    |--------------------------------------------------------------------------
    | Server Error
    |--------------------------------------------------------------------------
    |
    | The server failed to fulfill an apparently valid request.
    |
    */
    public const INTERNAL_SERVER_ERROR                     = 500;
    public const HTTP_STATUS_MESSAGE_INTERNAL_SERVER_ERROR = 'The server encountered an unexpected condition 
                                                            which prevented it from fulfilling the request.';

    protected $response = [];
    protected $user;

    /**
     * Standard format for successful API requests
     * @param $data
     * @param $success
     * @param $message
     * @param $http_status
     * @return \Illuminate\Http\JsonResponse
     */
    protected function apiSuccessResponse($data, $success = true, $message = '', $http_status = 200)
    {
        return response()->json(compact('success', 'message', 'http_status', 'data'), $http_status);
    }

    /**
     * Standard format for error API requests
     *
     * @param $success
     * @param $message
     * @param $http_status
     * @param $error_code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function apiErrorResponse($success = false, $message = '', $http_status = null, $error_code = null)
    {
        return response()->json(compact('success', 'message', 'http_status', 'error_code'), $http_status);
    }


    /**
     * Evaluate the resulting data from corresponding Model Repository
     *
     * @return response formatted
     */
    protected function checkResponseData($model, $responseData, $successMessage, $badRequest = null)
    {
        if ($badRequest) {
            return $this->apiErrorResponse(false, $responseData['error'], self::HTTP_STATUS_BAD_REQUEST, 'badRequest');
        }
        if ($responseData[$model]) {
            return $this->apiSuccessResponse($responseData, true, $successMessage, self::HTTP_STATUS_REQUEST_OK);
        }
        if (strpos($responseData['error'], 'not found') !== false) {
            return $this->apiErrorResponse(false, $responseData['error'], self::HTTP_STATUS_NOT_FOUND, $model . 'NotFound');
        }

        return $this->apiErrorResponse(false, $responseData['error'], self::INTERNAL_SERVER_ERROR, 'sqlError');
    }

    /**
     * Manual authentication. Alternative if you do not want to use the JWT Middleware
     *
     * @param Request $r
     * @return bool
     */
    protected function authenticate(Request $r)
    {
        try {
            if (! $user = JWTAuth::authenticate($r->token)) {
                $this->response = [
                    'status' => 404,
                    'error_code' => 'userNotFound',
                    'message'=>'User not found'
                ];
                return false;
            }
        } catch (TokenExpiredException $e) {
            $this->response = [
                'status' => 404,
                'error_code' => 'tokenExpired',
                'message'=>'Token expired'
            ];
            return false;
        } catch (TokenInvalidException  $e) {
            $this->response = [
                'status' => 404,
                'error_code' => 'invalidToken',
                'message'=>'Invalid Token'
            ];
            return false;
        } catch (JWTException $e) {
            $this->response = [
                'status' => 404,
                'error_code' => 'tokenAbsent',
                'message'=>'Token Absent '
            ];
            return false;
        } catch (\Exception $e) {
            $this->response     =   [
                'status' => 404,
                'error_code' => 'Unknown Error',
                'message'=>'Unknown error'
            ];
            return false;
        }
        // the token is valid and we have found the user via the sub claim
        return $user;
    }
}
