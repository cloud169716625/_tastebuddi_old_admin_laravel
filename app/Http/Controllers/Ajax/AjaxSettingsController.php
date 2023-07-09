<?php

namespace App\Http\Controllers\Ajax;

use App\Enums\PagesType;
use App\Helpers\APIClients\DigitalOcean\DoClient;
use App\Helpers\Utils;
use App\Models\Setting;
use App\Models\Settings\Settings;
use App\Models\Users\Users;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Validation\Rule;

class AjaxSettingsController extends AjaxBaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Save settings
     * @param Request $r
     * @return array
     */
    public function saveSettings( Request $r )
    {
        // check if if there is a setting with key
        if( ! $settings = ( new Settings )->withKey( $r->key ) ){
            $settings = new Settings();
        }

        if( ! $settings->store( $r ) ){
            return $this->responseError( $settings->getErrors( true ));
        }

        return $this->responseSuccess( ['settings' => $settings ]);
    }

    /**
     * Get all settings
     *
     * @param Request $r
     * @return array
     */
    public function getSettings( Request $r )
    {
        $settings   = Settings::all();
        return $this->responseSuccess( ['settings' => $settings ]);
    }

    /**
     * Setup subdomains for development environment
     *
     * @param Request $r
     * @return array
     */
    public function setUpSubDomains( Request $r )
    {
        // check first if subdomain already exists
        // add the subdomain as an entry

        $dev_subdomain      =  Settings::getValue( 'dev_subdomain' );
        $staging_subdomain  =  Settings::getValue( 'staging_subdomain' );

        $a_records      =  new DoClient( env( 'DO_API_KEY' ) , Settings::getValue( 'dev_server_domain' ) , Settings::getValue( 'dev_server_ip' )  );
        if( ! $dev_subdomain ){
            return $this->responseError( 'Dev subdomain must not be empty' );
        }

        if( ! $staging_subdomain ){
            return $this->responseError( 'Staging subdomain must not be empty' );
        }

        if( ! $a_records->addARecord( $dev_subdomain ) ){
            return $this->responseError( $a_records->getError() );
        }
        if( ! $a_records->addARecord( $staging_subdomain ) ){
            return $this->responseError( $a_records->getError() );
        }

        $content                = file_get_contents( base_path().'/vhost.conf');
        $dev_server_name        = Settings::getValue( 'dev_subdomain' ).'.'.Settings::getValue( 'dev_server_domain' );
        $staging_server_name    = Settings::getValue( 'staging_subdomain' ).'.'.Settings::getValue( 'dev_server_domain' );

        $content = str_replace( '{dev_server_name}' , $dev_server_name, $content );
        $content = str_replace( '{staging_server_name}' , $staging_server_name, $content );

        file_put_contents( env('VHOST_FILE_PATH'), $content );

        return $this->responseSuccess( [ 'response' => $a_records ] );
    }

    public function show(string $setting)
    {
        $setting = Setting::whereName($setting)->firstOrFail();

        return JsonResource::make($setting);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', Rule::in(PagesType::all())],
            'value' => ['nullable']
        ]);

        Setting::updateOrCreate([
            'name' => data_get($data, 'name')
        ], [
            'value' => data_get($data, 'value')
        ]);

        return response()->noContent();
    }

}
