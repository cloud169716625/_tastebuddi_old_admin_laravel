<?php

namespace App\Http\Controllers\API\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ReportCategoryResource;
use App\Models\ReportCategory;
use Spatie\QueryBuilder\QueryBuilder;

class ApiReportCategoriesController extends ApiBaseController
{
    /**
     * List categories
     */
    public function index(Request $request)
    {
        $categories = QueryBuilder::for(ReportCategory::class)
                        ->allowedFilters([
                            'type'
                        ])
                        ->paginate($request->perPage ?? 50);

        return ReportCategoryResource::collection($categories);
    }
}
