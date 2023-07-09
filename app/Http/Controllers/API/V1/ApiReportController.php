<?php

namespace App\Http\Controllers\API\V1;

use App\Actions\SubmitReport;
use App\Enums\ReportCategoryType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class ApiReportController extends ApiBaseController
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, SubmitReport $submitReport)
    {
        $request->validate([
            'report_type'   => ['bail', 'required', 'string', Rule::in(ReportCategoryType::all())],
            'report_id'     => ['bail', 'required'],
            'reason_id'     => ['bail', 'required', 'exists:report_categories,id'],
            'description'   => ['bail', 'nullable', 'string'],
            'attachments'   => ['bail', 'nullable', 'array'],
            'attachments.*' => ['mimes:jpeg,jpg,png,gif'],
            'photos'        => ['bail', 'nullable', 'array'], // Added to fix issue, since both mobile platform uses photos
            'photos.*'      => ['mimes:jpeg,jpg,png,gif']
        ]);

        $submitReport->execute(
            $request->input('report_type'),
            $request->input('report_id'),
            $request->input('reason_id'),
            $request->input('description'),
            $request->has('photos') ? $request->file('photos', []) : $request->file('attachments', [])
        );

        return $this->apiSuccessResponse(null);
    }
}
