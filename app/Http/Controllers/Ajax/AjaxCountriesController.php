<?php

namespace App\Http\Controllers\Ajax;

use App\Enums\MediaCollectionType;
use App\Helpers\Utils;
use App\Models\Items\Country;
use Illuminate\Http\Request;

use Intervention\Image\ImageManagerStatic as Image;
use PHPUnit\Framework\Constraint\Count;
use Illuminate\Support\Str;

class AjaxCountriesController extends AjaxBaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get country by id
     *
     * @param Request $r
     * @return array
     */

    public function getCountries( Request $request )
    {
        $searchQuery = $request->searchQuery;
        $searchFilter = $request->searchFilter;
        $pageLimit = $request->pageLimit;
        $currentSort = $request->currentSort;
        $sortDirection = 'ASC';

        if ($request->sortDirection == 'true') {
            $sortDirection = 'ASC';
        }
        if ($request->sortDirection == 'false') {
            $sortDirection = 'DESC';
        }

        $countries = Country::when($searchFilter == "country_name", function ($query) use ($searchQuery) {
            $query->where('country_name', 'like', "%$searchQuery%");
        })->when($searchFilter == "capital", function ($query) use ($searchQuery) {
            $query->where('capital', 'like', "%$searchQuery%");
        })
            ->orderBy($currentSort, $sortDirection)
            ->paginate($pageLimit);

        $data  = [
            'countries'         => $countries,
            'request'       => request()->all()
        ];

        return $this->responseSuccess( $data );
    }

    /**
     * Get a single country by country id
     *
     * @param Request $r
     * @return array
     */

    public function getCountry( Request $request )
    {
        // do permission checks here
        $country_id = Utils::recoverInt( $request->cid );

        $country = Country::where('country_id', $country_id)->first();

        $country->setAppends(['background_url', 'flag_url']);

        $data = [
            'country' => $country
        ];

        return $this->responseSuccess( $data );
    }


    public function saveCountry( Request $request )
    {

        $country = $request->country_id ? Country::find( $request->country_id ) : new Country();

        if( ! $country->store( $request )){
            // return $country->getErrors( true );
            return $this->responseError( $country->getErrors( ));
        }

        $data = [
            'country' => $country
        ];

        return $this->responseSuccess( $data, 'Country successfully saved' );
    }

    /**
     * Delete country
     * @param Request $request
     * @return array
     * @throws \Exception
     */

    public function deleteCountry( Request $request )
    {
        $country_id = $request->cid;

        $country = Country::where('country_id', $country_id)->delete();


        return $this->responseSuccess( [ 'cid' => $request->cid ] );
    }

    /**
     * @param Request $request
     * @return array
     */
    public function uploadBackgroundPhoto( Request $request )
    {
        $request->validate([
            'file' => 'required|image'
        ]);

        $country = Country::where('country_id', $request->country_id)->first();

        /** @var \Illuminate\Http\Testing\File */
        $image = $request->file;

        $name = Str::random(30);
        $hashName = explode('.', $image->hashName())[0];

        $country->addMediaFromRequest('file')
            ->usingName("{$name}{$hashName}")
            ->usingFileName($image->hashName())
            ->toMediaCollection(MediaCollectionType::COUNTRY_BACKGROUND);
    }

    public function uploadFlagPhoto( Request $request )
    {
        $request->validate([
            'file' => 'required|image'
        ]);

        $country = Country::where('country_id', $request->country_id)->first();

        /** @var \Illuminate\Http\Testing\File */
        $image = $request->file;

        $name = Str::random(30);
        $hashName = explode('.', $image->hashName())[0];

        $country->addMediaFromRequest('file')
            ->usingName("{$name}{$hashName}")
            ->usingFileName($image->hashName())
            ->toMediaCollection(MediaCollectionType::COUNTRY_FLAG_IMAGE);
    }

}
