<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UtilityController extends Controller
{
    /**
     * Used for DigitalOcean spaces testing
     *
     * @param Request $r
     * @return string
     */
    public function uploadToSpaces( Request $r )
    {
        $file_path = public_path().'/images/blank_face.jpg';
        $file_content = file_get_contents($file_path );

        $result =  \Storage::disk( 'spaces' )->put( '/static/a/b/c/d/bf.jpg', $file_content , 'public' );

        dd( $result );

        return $result;
    }

    /**
     * Used for slack notification testing
     * @throws \Exception
     */
    public function notifyThroughSlack()
    {
        throw new \Exception(" Error message test to slack ! " );
    }
    
    public function listDomainRecords( Request $r )
    {
        $guzzle = new \GuzzleHttp\Client(['base_uri' => 'https://api.digitalocean.com']);
        $response = $guzzle->get( 'v2/domains/flaneycrm.com/records', [
            'headers' => [ 'Authorization' => 'Bearer '.env( 'DO_API_KEY' ) ]
        ])->getBody();

        $data   = json_decode( $response );

        dd( $data );
    }
    

}