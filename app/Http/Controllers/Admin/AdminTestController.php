<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use Helpers\Layout;
use Illuminate\Http\Request;

class AdminTestController extends AdminBaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index( Request $r )
    {
        return view( 'admin.test.test_google_maps' );
    }





}
