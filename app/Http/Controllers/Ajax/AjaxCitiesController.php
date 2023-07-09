<?php

namespace App\Http\Controllers\Ajax;

use App\Helpers\Utils;
use App\Models\Items\City;
use Illuminate\Http\Request;
use App\Models\Items\Country;
use App\Repositories\Item\Contracts\ItemInterface;

// use Request;

class AjaxCitiesController extends AjaxBaseController
{
    /**
     * @var \App\Repositories\Item\Contracts\ItemInterface $itemInterface
     */
    private $itemInterface;

    /**
     * Create new AjaxBaseController instance
     *
     * @param \App\Repositories\Item\Contracts\ItemInterface $itemInterface
     */
    public function __construct(ItemInterface $itemInterface)
    {
        parent::__construct();
        $this->itemInterface = $itemInterface;
    }

    /**
     * Get city by id
     *
     * @param Request $r
     * @return array
     */

    public function getCities(Request $request)
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

        $cities =  City::when($searchFilter == "city_name", function ($query) use ($searchQuery) {
            $query->where('city_name', 'like', "%$searchQuery%");
        })->when($searchFilter == "city_code", function ($query) use ($searchQuery) {
            $query->where('city_code', 'like', "%$searchQuery%");
        })->when($searchFilter == 'country_id' && filled($searchQuery), function ($query) use ($searchQuery) {
            $query->where('country_id', $searchQuery);
        })
            ->orderBy($currentSort, $sortDirection)
            ->paginate($pageLimit);

        $data  = [
            'cities'         => $cities,
            'request'       => request()->all()
        ];

        return $this->responseSuccess($data);
    }

    /**
     * Get a single city by city id
     *
     * @param Request $r
     * @return array
     */

    public function getCity(Request $request)
    {
        // do permission checks here
        $city_id = Utils::recoverInt($request->cid);

        $city = City::where('city_id', $city_id)->first();

        $data = [
            'city' => $city
        ];

        return $this->responseSuccess($data);
    }


    public function saveCity(Request $request)
    {
        $city = $request->city_id ? City::find($request->city_id) : new City();

        if (! $city->store($request)) {
            return $this->responseError($city->getErrors());
        }

        $data = [
            'city' => $city
        ];

        return $this->responseSuccess($data, 'City successfully saved');
    }

    /**
     * Delete city
     * @param Request $request
     * @return array
     * @throws \Exception
     */

    public function deleteCity(Request $request)
    {
        $city_id = $request->cid;

        $city = City::where('city_id', $city_id)->delete();


        return $this->responseSuccess([ 'cid' => $request->cid ]);
    }


    public function getCountries(Request $request)
    {
        $courses = Country::all();

        $data  = [
            'countries' => $courses,
            'request' => request()->all()
        ];

        return $this->responseSuccess($data);
    }

    /**
     * Get items by City
     *
     * @param int $cityId
     * @return \Illuminate\Http\Response
     */
    public function getItemsByCity(int $cityId)
    {
        $items = $this->itemInterface->getItemsByCityId($cityId);

        return $this->responseSuccess($items);
    }
}
