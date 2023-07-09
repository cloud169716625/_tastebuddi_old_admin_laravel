<?php

namespace App\Interfaces\StorageServices;

use Illuminate\Http\Request;

interface StorageServiceInterface{

    public function uploadImage( Request $r , $input_name = 'photo' );
    public function uploadFile( Request $r );
    public function getUrl( );
    public function getError();

}