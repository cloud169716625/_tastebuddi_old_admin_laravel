<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\Items\Country;
use Helpers\Layout;
use Illuminate\Http\Request;

class AdminCountriesController extends AdminBaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index( Request $r )
    {
        return view( 'admin.countries.admin_countries_index' );
    }

//    public function addCountry(Request $r )
//    {
//        $this->layout->content = view( 'admin.countries.admin_countries_create' );
//        return $this->layout;
//    }

    public function country( $country_id, Request $r )
    {
        $this->layout->content = view( 'admin.countries.admin_countries_country', compact( 'country_id' ) );
        return $this->layout;
    }



}
