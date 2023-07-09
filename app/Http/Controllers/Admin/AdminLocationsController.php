<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

class AdminLocationsController extends AdminBaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index( Request $r )
    {
        return view( 'admin.locations.admin_locations_index' );
    }

    public function addLocation(Request $r )
    {
        $this->layout->content = view( 'admin.locations.admin_locations_create' );
        return $this->layout;
    }

    public function location( $location_id, Request $r )
    {
        $this->layout->content = view( 'admin.locations.admin_locations_location', compact( 'location_id' ) );
        return $this->layout;
    }



}
