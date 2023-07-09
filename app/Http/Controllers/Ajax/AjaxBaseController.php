<?php

namespace App\Http\Controllers\Ajax;


use App\Http\Controllers\Controller;

class AjaxBaseController extends Controller
{

    public function __construct()
    {

    }

    public function responseSuccess( $data = [] , $message = '' )
    {
        return [
            'success' => true,
            'message' => $message,
            'data' => $data
        ];
    }

    public function responseError( $message = '' )
    {
        return [
            'success' => false,
            'message' => $message
        ];
    }
}
