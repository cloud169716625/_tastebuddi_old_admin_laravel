<?php

namespace App\Http\Middleware;

use App\Interfaces\StorageServices\Disks\LocalStorageService;
use App\Interfaces\StorageServices\Disks\SpacesStorageService;
use Closure;

class Upload
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle( $request, Closure $next)
    {
        \App::bind( 'StorageService', function( $app )
        {
            switch( env( 'STORAGE' ) ){
                case 'spaces':
                    return new SpacesStorageService();
                break;
                case 'cloudinary':
                break;
                case 's3':
                default:
                    return new LocalStorageService();
                break;
            }
        });

        return $next($request);
    }
}
