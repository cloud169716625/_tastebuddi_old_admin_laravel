<?php

namespace App\Http\Controllers;

use App\Mail\NewUserMail;
use App\Models\Users\Users;
use Helpers\Layout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{

    protected $layout;

    public function __construct()
    {
        $admin_theme = env( 'ADMIN_THEME', 'appetiser' );
        \View::addLocation( base_path().'/resources/views/themes/'.$admin_theme );
        $this->layout = view( 'layouts.layout_auth' );
    }

    /**
     * @param Request $r
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function login( Request $r )
    {
        // this is a simple auth method
        // improve when needed
        // return "kuya josh";
        if( $r->isMethod( 'POST' ) ){

            if( $user = \Auth::attempt( [ 'email' => $r->email , 'password' => $r->pwd ] ) ) {
                return redirect( 'admin/items' );
            }else{
                return redirect( 'login' )
                ->with(
                    [
                        'has_error' => true,
                        'message'   => 'Invalid username or password',
                        'alert' => 'danger'
                    ]
                );
            }

        }
        Layout::loadVue();
        Layout::instance()->addScript( '/js/vue/auth/login.js' );
        return  view( 'auth.login' );
    }

    public function logout()
    {
        \Auth::logout();
        return redirect('login')->with(
            [ 'message' => 'You have successfully logged out' ]
        );
    }
}

