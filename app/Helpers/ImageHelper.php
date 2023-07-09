<?php

namespace Helpers;

class ImageHelper {

    /**
     * This will generate thumbnails on the same directory as the file
     * photo_path should be under the public directory
     * $photo_path can be an Image object or a full path to the file
     *
     * @param $photo_path
     * @param $filename
     * @return mixed
     */

    public static function generateThumbNails( $photo_path,  $options = [] )
    {

        $image      =   \Image::make( $photo_path )->encode('jpg', 80);
        $filename   =   basename( $photo_path );

        // set the width twice as the height
        /**
        if( ! empty( $options[ 'half_ratio' ] ) ){
            $image->fit( 800 , 400 , null , 'center' )
                ->save( $photo_path , 70 );
        }
        **/
        $height = $image->height();
        $width 	= $image->width();

        $ratio =  $width / $height;
        $extension = pathinfo(  $filename, PATHINFO_EXTENSION );

        $photo_directory = str_replace( $filename, '' , $photo_path );
        $thumb_directory = $photo_directory;

        if( ! is_dir( $thumb_directory ) ){
            mkdir( $thumb_directory , 755 , true );
        }
        //checks and resizes images that are bigger than 600 in height

        if( $height > 600 ){
            $image->resize( null , 600 ,
                function( $constraint ){
                    $constraint->aspectRatio();
                }
            )->save( $photo_path , 80 );
        }elseif( $width > 800 ){
            $image->resize( 800 , null,
                function( $constraint ){
                    $constraint->aspectRatio();
                }
            )->save( $photo_path , 80 );
        }

        /****** create thumb nail *****/
        $small = 'thumb_'.$filename;
        $position = $ratio < .77  ? 'top' : 'center';

        $small_image = $image->fit( 180, 180 , null , $position )
            ->save( $thumb_directory.'/'.$small , 70 );

        //$url = url( str_replace( public_path(), '', $thumb_directory ) );
        $photo_sizes['small'] = $small;

        $smallest = 'xs_'.$filename;
        $small_image->resize( 48 , 48 ,
            function( $constraint ){
                $constraint->aspectRatio();
            }
        )->save( $thumb_directory.'/'.$smallest , 70 );

        $photo_sizes['smallest'] = $smallest;

        return $photo_sizes;
    }

}
