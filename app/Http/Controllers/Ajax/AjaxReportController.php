<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Resources\ReportResource;
use App\Models\Items\Item;
use App\Models\Items\Recommendation;
use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\Sorts\ReportedByCustomSort;
use App\Models\Users\Users;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\Sorts\Sort;

class AjaxReportController extends AjaxBaseController
{
    /**
     * Create new instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get All Reports
     */
    public function index(Request $request)
    {
        $reports = QueryBuilder::for(Report::class)
                        ->with([
                            'reporter',
                            'reason',
                            'reportable'
                        ])
                        ->join('users', 'users.id', '=', 'reports.reported_by')
                        ->join('report_categories', 'report_categories.id', '=', 'reports.reason_id')
                        ->select(
                            'reports.*',
                            'report_categories.label',
                            DB::raw("CONCAT(users.first_name, ' ', users.last_name) as full_name")
                        )
                        ->allowedFilters([
                            AllowedFilter::scope('search'),
                            AllowedFilter::exact('reason_id'),
                            AllowedFilter::exact('reportable_type')
                        ])
                        ->allowedSorts([
                            'created_at',
                            AllowedSort::field('reporter', 'full_name'),
                            AllowedSort::field('reason', 'report_categories.label'),
                            AllowedSort::field('type', 'reportable_type')
                        ])
                        ->paginate($request->perPage ?? 10);

        return ReportResource::collection($reports);
    }

    /**
     * Delete Report
     */
    public function destroy(Report $report)
    {
        $report->delete();

        return response()->json([], Response::HTTP_NO_CONTENT);
    }

    /**
     * Show Report
     */
    public function show(Report $report): JsonResource
    {
        return new ReportResource(
            $report->load([
                'reportable',
                'reporter',
                'reason',
                'attachments'
            ])
        );
    }

    /**
     * Delete Reportable.
     * Note: should use SoftDeletes on Morhped Model.
     */
    public function destroyReportable(Report $report)
    {
        if ($report->reportable instanceof Recommendation) {
            $report->reportable->suggestedPrice()->delete();
            $report->reportable()->delete();

            return response()->json([], Response::HTTP_NO_CONTENT);
        }

        if ($report->reportable instanceof Users) {
            $report->reportable->disable();

            return response()->json([], Response::HTTP_NO_CONTENT);
        }
        
        if ($report->reportable instanceof Item) {
            $report->reportable->recommendations()->delete();
            $report->reportable->details()->delete();
            $report->reportable()->delete();
            
            return response()->json([], Response::HTTP_NO_CONTENT);
        }

        return response()->json([
            'message' => 'Disable for Model not yet configured.'
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
