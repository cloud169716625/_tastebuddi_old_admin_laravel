<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\Users\Users;
use Helpers\Layout;
use Illuminate\Http\Request;

class AdminUsersController extends AdminBaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index( Request $r )
    {
        // Layout::instance()->addScript( 'js/vue/admin/users/admin_users_index.js');
        return view( 'admin.users.admin_users_index' );
    }

    public function user( $user_id, Request $r )
    {
        // Layout::loadFileupload();
        // Layout::instance()->addScript( 'js/vue/admin/users/admin_user.js' );
        $this->layout->content = view( 'admin.users.admin_users_user', compact( 'user_id' ) );
        return $this->layout;
    }


}
