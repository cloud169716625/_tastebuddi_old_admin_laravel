<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use Helpers\Layout;
use Illuminate\Http\Request;

class AdminNotificationsController extends AdminBaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index( Request $r )
    {
        Layout::instance()->addScript( 'js/vue/admin/users/admin_notifications.js');
        $this->layout->content = view( 'admin.notifications.admin_notifications_index' );
        return $this->layout;
    }

}