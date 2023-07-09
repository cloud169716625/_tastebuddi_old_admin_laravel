<?php

namespace App\Interfaces\StorageServices\Disks;


use App\Interfaces\StorageServices\BaseStorageService;
use App\Interfaces\StorageServices\StorageServiceInterface;
use Helpers\ImageHelper;
use Illuminate\Http\Request;

class LocalStorageService extends BaseStorageService implements StorageServiceInterface
{
	protected $error;
	protected $url;
	protected $file_path;

	public function __construct()
	{

	}

    /**
     * Upload an image file to local server
     *
     * @param Request $request
     * @param string $input_name
     * @return $this|bool
     */
	public function uploadImage( Request $request, $input_name = 'photo' )
	{
        if( ! $file_path = $this->saveLocalCopy( $request , $input_name ) ){
            return false;
        }

        $image = \Image::make(  $file_path );
        $this->checkExifOrientation( $image );

        if( $request->compress ) {
            $image = $this->compressImage( $image , $file_path  );
        }

        if( $request->with_thumbnails ) {
            $this->thumbnails = $this->generateThumbNails( $image , $file_path );
        }

        $this->url = str_replace( public_path(), env( 'APP_URL' ) , $file_path );
        $this->file_path = $file_path;

        return $this;
	}

	public function getError( )
	{
	
	}

    public function uploadFile(Request $r)
    {

    }

    public function getUrl( )
    {
        return $this->url;
    }
}