<?php

namespace App\Http\Controllers\Ajax;

use App\Enums\ItemDetailStatus;
use App\Enums\MediaCollectionType;
use App\Http\Resources\ItemResource;
use App\Models\Items\Category;
use App\Models\Items\City;
use App\Models\Items\Item;
use App\Models\Items\Location;
use App\Models\Items\ItemDetail;
use App\Models\Items\Recommendation;
use App\Models\Users\Watchlist;
use Illuminate\Http\Request;
use Intervention\Image\ImageManagerStatic as Image;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;
use Illuminate\Support\Str;

// use Request;

class AjaxItemsController extends AjaxBaseController
{

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

    public function getItems(Request $request)
    {
        $searchQueryName = $request->searchQueryName;
        $searchQueryCity = $request->searchQueryCity;
        $searchQueryCategory = $request->searchQueryCategory;
        $queryNeedsReview = $request->queryNeedsReview;
        $pageLimit = $request->pageLimit;
        $currentSort = $request->currentSort;
        $sortDirection = 'ASC';

        if ($request->sortDirection == 'true') {
            $sortDirection = 'ASC';
        }
        if ($request->sortDirection == 'false') {
            $sortDirection = 'DESC';
        }

        $items = Item::when($searchQueryName, function ($query) use ($searchQueryName) {
            $query->where('item_name', 'like', "%$searchQueryName%");
        })->when($searchQueryCity, function ($query) use ($searchQueryCity) {
            $query->where('city_id', $searchQueryCity);
        })->when($searchQueryCategory, function ($query) use ($searchQueryCategory) {
            $query->where('category_id', $searchQueryCategory);
        })->when(filled($queryNeedsReview), function ($query) use ($queryNeedsReview) {
            $needsReview = (bool) $queryNeedsReview;

            if ($needsReview == true) {
                return $query->whereHas('details', function ($query) {
                    $query->withAllStatus()
                        ->whereNotNull('recommendation_id')
                        ->where('status', ItemDetailStatus::PENDING);
                });
            } else {
                return $query->whereDoesntHave('details', function ($query) {
                    $query->withAllStatus()
                        ->whereNotNull('recommendation_id')
                        ->where('status', ItemDetailStatus::PENDING);
                });
            }

        })
        ->withCount(['details' => function ($query) {
            $query->withAllStatus()
                ->whereNotNull('recommendation_id')
                ->where('status', ItemDetailStatus::PENDING);
        }])
            ->orderBy($currentSort, $sortDirection)
            ->paginate($pageLimit);

        $items->transform(function ($item) {
            $item->image_url = ($item->image) ? $item->image->getFullUrl() : null;
            $item->unsetRelation('image');

            return $item;
        });

        $data  = [
            'items' => $items,
            'request' => request()->all()
        ];

        return $this->responseSuccess($data);
    }

    /**
     * Get a single item by item id
     *
     * @param Request $r
     * @return array
     */

    public function getItem(Request $request)
    {
        // do permission checks here
        $item_id =  $request->item_id ;

        $item = Item::where('item_id', $item_id)->first();
        $watchers = Watchlist::where('item_id', $item_id)->count();
        $contributors = Recommendation::where('item_id', $item_id)->count();

        $data = [
            'item' => ItemResource::make($item->load('image')),
            'watchers' => $watchers,
            'contributors' => $contributors
        ];

        return $this->responseSuccess($data);
    }


    public function saveItem(Request $request)
    {
        $item_id = $request->item_id;
        $price = $request->price;
        $location_id = $request->location_id;
        $request->request->remove('price');
        $request->request->remove('location_id');

        $item = $item_id ? Item::find($item_id) : new Item();

        if (! $item->store($request)) {
            return $this->responseError($item->getErrors());
        }

        $details = new ItemDetail();
        $details->item_id = $item->item_id;
        $details->price = $price;
        $details->location_id = $location_id;
        $details->save();


        $data = [
            'item' => $item,
            'details' => $details
        ];



        return $this->responseSuccess($data, 'Item successfully saved');
    }

    public function updateItem(Request $request)
    {
        $item_id = $request->item_id;
        $details = null;

        if ($request->has('price')) {
            $price = $request->price;
            $location_id = $request->location_id;
            $request->request->remove('price');
            $request->request->remove('location_id');
        }

        $item = $item_id ? Item::find($item_id) : new Item();

        if (! $item->store($request)) {
            return $this->responseError($item->getErrors());
        }

        if ($request->has('price')) {
            $details = new ItemDetail();
            $details->item_id = $item->item_id;
            $details->price = $price;
            $details->location_id = $location_id;
            $details->save();
        }

        $data = [
            'item' => ItemResource::make($item->fresh()->load('image')),
            'details' => $details
        ];

        return $this->responseSuccess($data, 'Item successfully saved');
    }

    /**
     * Delete item
     * @param Request $request
     * @return array
     * @throws \Exception
     */

    public function deleteItem(Request $request)
    {
        $item_id = $request->item_id;

        $item = Item::where('item_id', $item_id)->firstOrFail();

        $item->recommendations()->delete();
        $item->details()->delete();
        $item->delete();

        return $this->responseSuccess([ 'item_id' => $request->iid ]);
    }

    public function getCategories(Request $request)
    {
        $categories = Category::all();

        $data  = [
            'categories' => $categories,
            'request' => request()->all()
        ];

        return $this->responseSuccess($data);
    }

   public function getCities(Request $request)
    {
        if ($request->has('city_id')) {
            $cities = City::where('city_id', $request->city_id)->first();
        } else if ($request->has('country_name') && filled($request->country_name)) {
            $cities = City::whereHas('country', function ($query) use ($request) {
                $query->where('country_name', $request->country_name);
            })->get();
        } else {
            $cities = City::all();
        }

        $data = [
            'cities' => $cities,
            'request' => request()->all()
        ];

        return $this->responseSuccess($data);
    }

    public function getLocations(Request $request)
    {
        if (!$request->has('city_id')) {
            return $this->responseError('City is required');
        }
        $locations = Location::where('city_id', $request->city_id)->get();

        $data  = [
            'locations' => $locations,
        ];

        return $this->responseSuccess($data);
    }

    public function getItemDetails(Request $request)
    {
        $item_id = $request->item_id;
        $details = ItemDetail::leftJoin('locations', function ($join) use ($item_id) {
            $join->on('locations.location_id', '=', 'item_details.location_id');
            $join->where('item_details.item_id', '=', $item_id);
        })
        ->where('item_details.item_id', $item_id)
        ->when($request->has('name') && filled($request->name), function ($query) use ($request) {
            $query->whereHas('recommendation.user', function ($query) use ($request) {
                $query->where('first_name', 'LIKE', "%{$request->name}%")
                    ->orWhere('last_name', 'LIKE', "%{$request->name}%");
            });
        })
        ->with(['recommendation' => function ($query) {
            $query->allSuggestedPrice()->with('user');
        }])
        ->withAllStatus()
        ->get();

        $details->transform(function (ItemDetail $itemDetail) {
            $itemDetail->isVerifiedBusinessDetail = $itemDetail->verifiedBusinessItem()->exists();
            return $itemDetail;
        });

        $data = ['details' => $details];
        return $this->responseSuccess($data);
    }


    public function addItemDetails(Request $request)
    {
        $item_id =  $request->item_id ;

        $details = new ItemDetail;
        $details->item_id = $item_id;
        $details->location_id = $request->location_id;
        $details->price = $request->price;

        $details->save();

        $data = ['details' => $details];

        return $this->responseSuccess($data);
    }

    public function uploadItemPhoto(Request $request)
    {
        $request->validate([
            'file' => 'required'
        ]);

        $item_id = $request->item_id;

        if ($request->file('file')) {
            $item = Item::where('item_id', $item_id)->first();

            /** @var \Illuminate\Http\Testing\File */
            $image = $request->file;

            $name = Str::random(30);
            $hashName = explode('.', $image->hashName())[0];

            $item->addMediaFromRequest('file')
                ->usingName("{$name}{$hashName}")
                ->usingFileName($image->hashName())
                ->toMediaCollection(MediaCollectionType::ITEM_IMAGE);

            return ItemResource::make($item->fresh()->load('image'));
        }
    }

    public function deleteDetail(Request $request)
    {
        if (!$detail = ItemDetail::query()->withAllStatus()->find($request->item_detail_id)) {
            return $this->responseError('Item Detail not found');
        }

        if ($detail->recommendation()->exists()) {
            $detail->recommendation->delete();
        }

        $detail->delete();

        return $this->responseSuccess(['question_id' => $request->question_id]);
    }


    private function notify()
    {
        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60*20);

        $notificationBuilder = new PayloadNotificationBuilder('my title');
        $notificationBuilder->setBody('Hello world')
            ->setSound('default');

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData(['a_data' => 'my_data']);

        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        $token = "a_registration_from_your_database";

        $downstreamResponse = FCM::sendTo($token, $option, $notification, $data);

        $downstreamResponse->numberSuccess();
        $downstreamResponse->numberFailure();
        $downstreamResponse->numberModification();

        // return Array - you must remove all this tokens in your database
        $downstreamResponse->tokensToDelete();

        // return Array (key : oldToken, value : new token - you must change the token in your database)
        $downstreamResponse->tokensToModify();

        // return Array - you should try to resend the message to the tokens in the array
        $downstreamResponse->tokensToRetry();

        // return Array (key:token, value:error) - in production you should remove from your database the tokens
        $downstreamResponse->tokensWithError();
    }
}
