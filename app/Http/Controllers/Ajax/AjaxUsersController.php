<?php

namespace App\Http\Controllers\Ajax;

use App\Enums\MediaCollectionType;
use App\Helpers\Utils;
use App\Http\Resources\RecommendationResource;
use App\Models\Items\Recommendation;
use App\Models\Users\Users;
use App\User;
use Illuminate\Http\Request;

use Intervention\Image\ImageManagerStatic as Image;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Support\Str;

class AjaxUsersController extends AjaxBaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get users by id
     *
     * @param Request $r
     * @return array
     */

    public function getUsers(Request $request)
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

        $users =  Users::when($searchFilter == "first_name", function ($query) use ($searchQuery) {
            $query->where('first_name', 'like', "%$searchQuery%");
        })->when($searchFilter == "last_name", function ($query) use ($searchQuery) {
            $query->where('last_name', 'like', "%$searchQuery%");
        })->when($searchFilter == "email", function ($query) use ($searchQuery) {
            $query->where('email', 'like', "%$searchQuery%");
        })
            ->orderBy($currentSort, $sortDirection)
            ->paginate($pageLimit);

        $data  = [
            'users' => $users,
            'request' => request()->all()
        ];

        return $this->responseSuccess($data);
    }

    /**
     * Get a single user by user id
     *
     * @param Request $r
     * @return array
     */

    public function getUser(Request $request)
    {
        // do permission checks here
        $user_id = Utils::recoverInt($request->uid);
        $user = ( new Users() )->f($user_id);

        $data = [
            'user' => $user
        ];

        return $this->responseSuccess($data);
    }


    public function saveUser(Request $request)
    {
        $user = $request->id ? Users::find($request->id) : new Users();

        if (! $user->store($request)) {
            return $this->responseError($user->getErrors(true));
        }

        $data = [
            'user' => $user
        ];

        return $this->responseSuccess($data, 'User successfully saved');
    }

    /**
     * Delete user
     * @param Request $request
     * @return array
     * @throws \Exception
     */

    public function deleteUser(Request $request)
    {
        // Check first if current user has permission to delete this user
        // if (! $user = ( new Users )->f($request->user_id)) {
        //     return $this->responseError('User not found');
        // }

        // $user->delete();

        // return $this->responseSuccess([ 'user_id' => $request->user_id ]);

        $user = User::withTrashed()->find($request->user_id);

        if (! $user) {
            return $this->responseError('User not found');
        }

        $user->delete();

        return $this->responseSuccess([ 'user_id' => $request->user_id ]);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function uploadProfilePhoto(Request $request)
    {
        if ($request->file('file')) {
            $user   = ( new Users() )->f($request->uid);

            /** @var \Illuminate\Http\Testing\File */
            $image = $request->file;

            $name = Str::random(30);
            $hashName = explode('.', $image->hashName())[0];

            $user->addMediaFromRequest('file')
                ->usingName("{$name}{$hashName}")
                ->usingFileName($image->hashName())
                ->toMediaCollection(MediaCollectionType::USER_AVATAR);
            }
    }

    /**
     * Get a single user by user id
     *
     * @param Request $r
     * @return array
     */

    public function getUserRecommendations(Request $request)
    {
        $userId = $request->userId;

        $recommendations = QueryBuilder::for(Recommendation::class)
                            ->withTrashed()
                            ->with('location', 'item')
                            ->where('user_id', $userId)
                            ->paginate(10);

        return RecommendationResource::collection($recommendations);
    }

    /**
     * Activate User
     */
    public function activate(int $userId)
    {
        $user = User::withTrashed()->findOrFail($userId);

        $user->restore();

        return $this->responseSuccess();
    }

    /**
     * Enable User
     */
    public function enable(int $userId)
    {
        $user = Users::findOrFail($userId);

        $user->enable();

        return $this->responseSuccess();
    }

    /**
     * Disable User
     */
    public function disable(int $userId)
    {
        $user = Users::findOrFail($userId);

        $user->disable();

        return $this->responseSuccess();
    }

    /**
     * Activate Recommendation
     */
    public function deactivateRecommendation(int $userId, Recommendation $recommendation)
    {
        $recommendation->suggestedPrice()->delete();
        $recommendation->delete();

        return $this->responseSuccess();
    }
}
