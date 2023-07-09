<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use Helpers\Layout;

class AdminBaseController extends Controller
{
    protected $layout;

    public function __construct()
    {

        $admin_theme = env( 'ADMIN_THEME', 'appetiser' );
        \View::addLocation( base_path().'/resources/views/themes/'.$admin_theme );
        $this->layout   = view( 'layouts.layout_admin' );

        Layout::loadVue();
        Layout::loadToastr();

    }
}
