<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use Helpers\Layout;
use Illuminate\Http\Request;

class AdminItemsController extends AdminBaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index( Request $r )
    {
        return view( 'admin.items.admin_items_index' );
    }

    public function addItem(Request $r )
    {
        $this->layout->content = view( 'admin.items.admin_items_create' );
        return $this->layout;
    }

    public function item( $item_id, Request $r )
    {
        $this->layout->content = view( 'admin.items.admin_items_item', compact( 'item_id' ) );
        return $this->layout;
    }



}
