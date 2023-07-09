<?php

namespace App\Interfaces\StorageServices\Disks;


use App\Interfaces\StorageServices\BaseStorageService;
use App\Interfaces\StorageServices\StorageServiceInterface;
use Illuminate\Http\Request;

class SpacesStorageService extends BaseStorageService implements StorageServiceInterface
{
    protected $url;
    protected $file_path;

	public function __construct()
	{

	}

	public function getError( )
	{
        return $this->error;
	}

    /**
     * Upload a file to DigitalOcean Spaces
     *
     * @param Request $request
     * @param string $input_name
     * @return bool|mixed
     */
    public function uploadImage( Request $request, $input_name = 'photo' )
    {
        // save the uploaded image directly to Spaces
        // If you need to compress files and generate Thumbnails
        // you need to save the file first on local

        if( $request->skip_local_copy ){
            //return $this->directUpload( $request );
        }

        // save a copy of the image on local for processing
        // Spaces doesnt have API's yet for thumbnail generation
        if( ! $file_path = $this->saveLocalCopy( $request , $input_name ) ){
            return false;
        }

        $image = \Image::make(  $file_path );
        $this->checkExifOrientation( $image );

        if( $request->compress ){
            $image = $this->compressImage( $image , $file_path  );
        }

        // push the files to Spaces
        $relative_path  = str_replace( public_path(), '/' , $file_path );
        $content        = file_get_contents( $file_path );

        \Storage::disk('spaces')->put( $relative_path, $content,  'public' );
        $this->url  = str_replace( env( 'DO_SPACES_ENDPOINT' ) , env( 'DO_SPACES_BASE_URL' ) , \Storage::disk( 'spaces' )->url( $relative_path ) );

        // create thumbnails if needed
        if( $request->with_thumbnails ){
            $this->thumbnails = $this->generateThumbNails( $image , $file_path );
        }

        foreach( $this->thumbnails as $thumb_path ){
            $image_content  = file_get_contents( $thumb_path );
            $relative_path  = str_replace( public_path(), '/' , $thumb_path );

            \Storage::disk('spaces')->put( $relative_path, $image_content,  'public' );
        }

        return $this->url;
    }

    /**
     * Direct upload to spaces without saving any copy on local
     * @param $request
     * @param string $input_name
     * @return mixed
     */
    private function directUpload( $request , $input_name = 'photo' )
    {
        $image_path = substr( $this->generateUserImagePath() , 0, -1 );

        $url = \Storage::disk( 'spaces' )
            ->put( $image_path , $request->$input_name , 'public' );

        $this->url = env('DO_SPACES_BASE_URL').'/'.$url;

        return $this->url;
    }

    public function uploadFile(Request $r)
    {

    }

    public function getUrl( )
    {
        return $this->url;
    }
}