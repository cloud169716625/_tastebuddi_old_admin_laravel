<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

class AdminCategoriesController extends AdminBaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index( Request $r )
    {
        return view( 'admin.categories.admin_categories_index' );
    }




}
