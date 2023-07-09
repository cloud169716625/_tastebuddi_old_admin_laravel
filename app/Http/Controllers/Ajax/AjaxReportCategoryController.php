<?php

namespace App\Http\Controllers\Ajax;

use Illuminate\Http\Request;
use App\Http\Resources\ReportCategoryResource;
use App\Models\ReportCategory;
use Spatie\QueryBuilder\QueryBuilder;

class AjaxReportCategoryController extends AjaxBaseController
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $categories = QueryBuilder::for(ReportCategory::class)
                        ->paginate($request->perPage ?? 10);

        return $this->responseSuccess(
            ReportCategoryResource::collection($categories)
        );
    }
}
