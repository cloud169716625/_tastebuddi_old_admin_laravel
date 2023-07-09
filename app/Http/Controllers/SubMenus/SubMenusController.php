<?php

namespace App\Http\Controllers\SubMenus;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SubMenusController
{
    /**
     * Submenus for authenticated users
     *
     * @param Request $r
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public static function render( Request $r )
    {
        if( ! $user = $r->user() ){
            return false;
        }

        $submenu_path = ('submenus.submenu_'.strtolower( $user->user_type ) );
        return view( $submenu_path );
    }
}