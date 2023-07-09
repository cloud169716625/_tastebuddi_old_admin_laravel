<?php

namespace App\Http\Controllers\Ajax;

use App\Helpers\Utils;
use App\Models\Items\Category;
use Illuminate\Http\Request;

class AjaxCategoriesController extends AjaxBaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get countries
     *
     * @param Request $r
     * @return array
     */

    public function getCategories(Request $request)
    {
        $searchQuery = $request->searchQuery;
        $pageLimit = $request->pageLimit;
        $currentSort = $request->currentSort;
        $sortDirection = 'ASC';

        if ($request->sortDirection == 'true') {
            $sortDirection = 'ASC';
        }
        if ($request->sortDirection == 'false') {
            $sortDirection = 'DESC';
        }

        $categories =  Category::when($searchQuery, function ($query) use ($searchQuery) {
            $query->where('category_name', 'like', "%$searchQuery%");
        })
            ->ordered()
            ->orderBy($currentSort, $sortDirection)
            ->paginate($pageLimit);

        $data  = [
            'categories'=> $categories,
            'request' => request()->all()
        ];

        return $this->responseSuccess($data);
    }

    /**
     * Get a single category by category id
     *
     * @param Request $r
     * @return array
     */

    public function getCategory(Request $request)
    {
        // do permission checks here
        $category_id = Utils::recoverInt($request->category_id);

        $category = Country::where('category_id', $category_id)->first();

        $data = [
            'category' => $category
        ];

        return $this->responseSuccess($data);
    }


    public function saveCategory(Request $request)
    {

        $category = $request->category_id ? Category::find($request->category_id) : new Category();

        if (! $category->store($request)) {
            return $this->responseError($category->getErrors());
        }

        $data = [
            'category' => $category
        ];

        return $this->responseSuccess($data, 'Category successfully saved');
    }

    /**
     * Delete country
     * @param Request $request
     * @return array
     * @throws \Exception
     */

    public function deleteCategory(Request $request)
    {
        $category_id = $request->category_id;

        Category::where('category_id', $category_id)->delete();

        return $this->responseSuccess([ 'category_id' => $request->category_id ]);
    }

    /**
     * Change Ordering
     */
    public function order(Request $request, Category $category)
    {
        $data = $request->validate([
            'order' => 'boolean|required'
        ]);

        $order = data_get($data, 'order');

        if ($order) {
            $category->moveOrderUp();
        } else {
            $category->moveOrderDown();
        }

        return $this->responseSuccess($category->fresh());
    }
}
