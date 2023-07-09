<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\Items\City;
use Helpers\Layout;
use Illuminate\Http\Request;

class AdminCitiesController extends AdminBaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index( Request $r )
    {
        return view( 'admin.cities.admin_cities_index' );
    }

    public function addCity(Request $r )
    {
        $this->layout->content = view( 'admin.cities.admin_cities_create' );
        return $this->layout;
    }

    public function city( $city_id, Request $r )
    {
        $this->layout->content = view( 'admin.cities.admin_cities_city', compact( 'city_id' ) );
        return $this->layout;
    }



}
