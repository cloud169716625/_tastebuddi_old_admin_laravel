<?php

namespace App\Models\Traits;

use App\Models\Users\Users;
use App\Models\Report;
use App\Enums\MediaCollectionType;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use JWTAuth;

trait CanBeReported
{
    /**
     * The list of reports that was filed against the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function reports()
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    /**
     * File a report as the authenticated user.
     *
     * @param integer $reasonId
     * @param string $description
     * @param array $attachments
     * @return Report
     */
    public function report(int $reasonId, string $description = null, array $attachments = []) : Report
    {
        return $this->reportAsUser(JWTAuth::toUser(), $reasonId, $description, $attachments);
    }

    /**
     * File a report as the specified user.
     *
     * @param integer $reasonId
     * @param string $description
     * @param array $attachments
     * @return Report
     */
    public function reportAsUser(Users $user, int $reasonId, string $description = null, array $attachments = []): Report
    {
        return DB::transaction(function () use ($user, $reasonId, $description, $attachments) {
            $report = new Report();
            $report->reason_id    = $reasonId;
            $report->description  = $description;
            $report->reported_by  = $user->getKey();

            $this->reports()->save($report);

            foreach ($attachments as $attachment) {
                /** @var \Illuminate\Http\File */
                $image = $attachment;

                $name = Str::random(30);
                $hashName = explode('.', $image->hashName())[0];

                $report->addMedia($attachment)
                    ->usingName("{$name}{$hashName}")
                    ->usingFileName($image->hashName())
                    ->toMediaCollection(MediaCollectionType::REPORT_ATTACHMENTS);
            }

            return $report;
        });
    }

    /**
     * Check if is reported by current authenticated user.
     *
     * @return boolean
     */
    public function isReported()
    {
        return $this->isReportedAsUser(JWTAuth::toUser());
    }

    /**
     * Check if reported by user.
     *
     * @param User $user
     * @return boolean
     */
    public function isReportedAsUser(Users $user)
    {
        return $this->reports()->whereReportedBy($user->getKey())->exists();
    }

    /**
     * Filter reported by current user.
     *
     * @param Builder $query
     * @return void
     */
    public function scopeAppendIsReported(Builder $query)
    {
        $query->appendIsReportedAsUser(JWTAuth::toUser());
    }

    /**
     * Filter reported by specified user.
     *
     * @param Builder $query
     * @param User $user
     * @return void
     */
    public function scopeAppendIsReportedAsUser(Builder $query, Users $user)
    {
        $query->addSelect([
            'is_reported' => Report::query()
                ->selectRaw('count(id) as is_reported')
                ->where((new Report)->qualifyColumn('reportable_type'), $this->getMorphClass())
                ->whereColumn((new Report)->qualifyColumn('reportable_id'), $this->qualifyColumn('id'))
                ->take(1)
        ]);

        $query->withCasts([
            'is_reported' => 'boolean'
        ]);
    }
}
