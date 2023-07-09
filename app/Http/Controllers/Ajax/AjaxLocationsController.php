<?php

namespace App\Http\Controllers\Ajax;

use App\Actions\CreateItem;
use App\Http\Requests\CreateItemFromLocationRequest;
use App\Models\Items\ItemDetail;
use App\Models\Items\Location;
use Illuminate\Http\Request;

class AjaxLocationsController extends AjaxBaseController
{
    /**
     * Create new AjaxBaseController instance
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get item by id
     *
     * @param Request $r
     * @return array
     */

    public function getLocations(Request $request)
    {
        $searchQuery = $request->searchQuery;
        $searchFilter = $request->searchFilter;
        $pageLimit = $request->pageLimit;
        $currentSort = $request->currentSort;
        $sortDirection = 'ASC';
        $filters = json_decode($request->filters);

        if ($request->sortDirection == 'true') {
            $sortDirection = 'ASC';
        }
        if ($request->sortDirection == 'false') {
            $sortDirection = 'DESC';
        }

        $locations =  Location::when($searchFilter == "location", function ($query) use ($searchQuery) {
            $query->where('locations.location', 'like', "%$searchQuery%");
        })
        ->when(isset($filters->is_verified), function ($query) use ($filters) {
            $query->where('locations.is_verified', $filters->is_verified);
        })
        ->when(isset($filters->city_id), function ($query) use ($filters) {
            $query->where('locations.city_id', $filters->city_id);
        })
        ->when(isset($filters->country_id), function ($query) use ($filters) {
            $query->join('cities', 'cities.city_id', 'locations.city_id')
                ->where('cities.country_id', $filters->country_id);
        })
        ->orderBy($currentSort, $sortDirection)
        ->paginate($pageLimit);

        $data  = [
            'locations' => $locations,
            'request' => request()->all()
        ];
        return $this->responseSuccess($data);
    }

    public function getAllLocations(Request $request)
    {
        $locations =  Location::get();

        $data  = [
            'locations'         => $locations
        ];

        return $this->responseSuccess($data);
    }

    /**
     * Get a single location by location id
     *
     * @param Request $r
     * @return array
     */

    public function getLocation(Request $request)
    {
        $location_id =  $request->location_id ;

        $location = Location::where('location_id', $location_id)->with('city.country')->first();

        $data = [
            'location' => $location
        ];

        return $this->responseSuccess($data);
    }


    public function saveLocation(Request $request)
    {
        $location_id = $request->location_id;

        $location = $location_id ? Location::find($location_id) : new Location();

        if (! $location->store($request)) {
            return $this->responseError($location->getErrors());
        }

        $data = [
            'location' => $location
        ];

        return $this->responseSuccess($data, 'Location successfully saved');
    }

    /**
     * Delete location
     * @param Request $request
     * @return array
     * @throws \Exception
     */
    public function deleteLocation(Request $request)
    {
        if (!$location = Location::find($request->location_id)) {
            return $this->responseError('Location not found');
        }

        $location->delete();

        return $this->responseSuccess(['question_id' => $request->question_id]);
    }

    /**
     * Verify/Unverify location
     *
     * @param \App\Models\Items\Location $location
     * @return \Illuminate\Http\Response
     */
    public function verifyUnverify(Location $location)
    {
        $location->update(['is_verified' => !$location->is_verified]);

        return $this->responseSuccess($location);
    }

    /**
     * Get Item Details under Location
     *
     * @param \App\Models\Items\Location
     * @return \Illuminate\Http\Response
     */
    public function getLocationItemDetails(Location $location)
    {
        $itemDetails = $location
                        ->itemDetails()
                        ->with(['item', 'recommendation' => function ($query) {
                            $query->allSuggestedPrice()->with('user');
                        }])
                        ->withAllStatus()
                        ->get()
                        ->map(function (ItemDetail $itemDetail) {
                            $itemDetail->isVerifiedBusinessDetail = $itemDetail->verifiedBusinessItem()->exists();

                            return $itemDetail;
                        });

        return $this->responseSuccess($itemDetails);
    }


    /**
     * Create new item and add item details under location.
     *
     * @param \App\Http\Requests\CreateItemFromLocationRequest
     * @param \App\Models\Items\Location
     * @return \Illuminate\Http\Response
     */
    public function createItem(CreateItemFromLocationRequest $request, Location $location)
    {
        $data = $request->validated();

        (new CreateItem())($location, $data, false);

        return $this->responseSuccess([]);
    }
}
