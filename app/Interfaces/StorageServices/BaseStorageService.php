<?php

namespace App\Interfaces\StorageServices;


use App\Helpers\Utils;
use App\Interfaces\StorageServices\StorageServiceInterface;
use Illuminate\Http\Request;

class BaseStorageService
{
    protected $error;
    protected $thumbnails = [];
    /**
     * Save a copy of the uploaded file into the local system
     *
     * @param Request $request
     * @param string $input_name
     * @param bool $with_thumbnails
     * @return bool|string
     */
	public function saveLocalCopy( Request $request , $input_name = 'photo' )
	{
        $validator = \Validator::make( $request->all(), [ $input_name => 'required|mimes:jpeg,jpg,png' ] );

        if( $validator->fails() ){
            $this->error =  $validator->errors()->first();
            return false;
        }

        $ext = $request->$input_name->getClientOriginalExtension();

        // rename photo files if you like
        $new_filename   =   str_random( 16 ).'.'.$ext;
        $destination    =   rtrim( $this->generateUserImagePath(),"/");
        $destination    =   substr( $destination ,1  );

        // Defaults to a storage path
        // Do not forget to call php artisan storage:link

        try{
            $file_path = $request->file( $input_name )
                ->storeAs( $destination, $new_filename, 'public' );
        }catch( \Exception $e ){
            $this->error =   $e->getMessage() ;
            return false;
        }

        $absolute_path  =  public_path().'/storage/'.$file_path;
	    return $absolute_path;
	}

    public function processImage( $image_file_path , $request  )
    {
        $image = \Image::make(  $image_file_path );


        return $image;
    }

    protected function compressImage( $image, $image_file_path )
    {

        $height = $image->height();
        $width 	= $image->width();

        $fit_size = $request->fit_size ?? ( $height < 720 ? $height : 720 ) ;

        $ratio      = $width / $height;

        //checks and resizes images
        // default compression to 720px height

        if( $height > 720 ){
            $image->resize( null , 720 ,
                function( $constraint ){
                    $constraint->aspectRatio();
                }
            )->save(  $image_file_path , 80 );
        }elseif( $width > 1024 ){
            $image->resize( 1024 , null,
                function( $constraint ){
                    $constraint->aspectRatio();
                }
            )->save(  $image_file_path , 80 );
        }


        // you need to adjust this to handle images that has large difference in aspect ratio

        $image->fit( $fit_size )->save( $image_file_path );

        return $image;
    }

    /**
     * User image path is designed so that it would be easy to have
     * a daily backup for local storage
     * without downloading everything
     *
     * @return string
     *
     */
    protected function generateUserImagePath( )
    {
        $md     =  date( 'md' );
        $y      =  date( 'Y' );

        if( $user = request()->user() ){
            return '/images/'.$y.'/'.$md.'/'.Utils::convertInt( $user->id ).'/';
        }else{
            return '/images/'.$y.'/'.$md.'/';
        }

    }

    /**
     * Check image exif orientation and adjust if needed
     * @param $image
     */
    protected function checkExifOrientation( $image )
    {
        $orientation = $image->exif( 'Orientation' );
        switch( $orientation ){
            case 3:
            case 4:
                $image->rotate( 180 );
                $image->save();
                break;
            case 7:
            case 8:
                $image->rotate( 90 );
                $image->save();
                break;
            case 5:
            case 6:
                $image->rotate( 270 );
                $image->save();
                break;
            default:
        }
    }

    /**
     * Generate image thumbnails
     * @param $image
     * @param $file_path
     * @return mixed
     */

    protected function generateThumbnails( $image , $file_path )
    {
        $filename   =   basename( $file_path );
        $path_parts =   pathinfo( $file_path );

        $height = $image->height();
        $width 	= $image->width();

        $ratio      = $width / $height;
        $photo_directory    = $path_parts['dirname'];

        // you may set the thumb directory same as the directory where the photo was saved
        // or you may opt for another lower level directory

        $thumb_directory    = $photo_directory;

        if( $photo_directory != $thumb_directory ){
            if( ! is_dir( $thumb_directory ) ){
                mkdir( $thumb_directory , 755 , true );
            }
        }

        // Creating a square thumbnail 1:1 aspect ratio
        // On many instances a 16:9 aspect ratio would also be ideal just modify the code below if you want

        $small = 'thumb_'.$filename;
        $position = $ratio < .77  ? 'top' : 'center';

        $photo_sizes['small'] = $thumb_directory.'/'.$small;

        $small_image = $image->fit( 192, 192 , null , $position )
            ->save( $photo_sizes['small'] , 70 );

        $smallest = 'xs_'.$filename;
        $photo_sizes['smallest'] = $thumb_directory.'/'.$smallest;

        $small_image->resize( 64, 64,
            function( $constraint ){
                $constraint->aspectRatio();
            }
        )->save( $photo_sizes['smallest'] , 70 );
        return $photo_sizes;
    }
    
    public function getError()
    {
        return $this->error;
    }

    public function getThumbnails(){
        return $this->thumbnails;
    }

}