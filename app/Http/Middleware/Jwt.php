<?php

namespace App\Http\Middleware;

use App\Http\Controllers\API\V1\ApiBaseController;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Exception;
use JWTAuth;
use Closure;

class Jwt extends ApiBaseController
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return $this->apiErrorResponse(false, 'User Not Found', ApiBaseController::HTTP_STATUS_NOT_FOUND, 'userNotFound');
            } else {
                if ($user->disabled_at) {
                    return $this->apiErrorResponse(false, 'Your account has been disabled.', ApiBaseController::HTTP_STATUS_BAD_REQUEST, 'accoutDisabled');
                }
            }
        } catch (TokenInvalidException $e) {
            return $this->apiErrorResponse(false, 'Invalid Token', ApiBaseController::HTTP_STATUS_NOT_FOUND, 'invalidToken');
        } catch (TokenExpiredException $e) {
            /*             try {
                            $refreshed = JWTAuth::refresh(JWTAuth::getToken());
                            $user = JWTAuth::setToken($refreshed)->toUser();
                            header('Authorization: Bearer ' . $refreshed);
                        } catch (JWTException $e) {
                            return response()->json([
                                'code'   => 103,
                                'response' => null // nothing to show
                            ]);
                        } */
            return $this->apiErrorResponse(false, 'Token Expired', ApiBaseController::HTTP_STATUS_NOT_FOUND, 'tokenExpired');
        } catch (JWTException $e) {
            return $this->apiErrorResponse(false, 'Token Absent', ApiBaseController::HTTP_STATUS_NOT_FOUND, 'tokenAbsent');
        } catch (Exception $e) {
            return $this->apiErrorResponse(false, 'Unknown Error', ApiBaseController::HTTP_STATUS_NOT_FOUND, 'unknownError');
        }

        return $next($request);
    }
}
