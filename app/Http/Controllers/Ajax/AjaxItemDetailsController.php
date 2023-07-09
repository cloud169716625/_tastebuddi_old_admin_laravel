<?php

namespace App\Http\Controllers\Ajax;

use App\Actions\NotifyWatchersOnPriceChange;
use App\Enums\ItemDetailStatus;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Items\Item;
use App\Models\Items\ItemDetail;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class AjaxItemDetailsController extends AjaxBaseController
{
    /**
     * Create a new instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Update Item Detail
     */
    public function update(Request $request, int $itemDetailId)
    {
        $itemDetail = ItemDetail::query()
                        ->withAllStatus()
                        ->findOrFail($itemDetailId);

        $data = $request->validate([
            'status' => [
                'required',
                Rule::in(ItemDetailStatus::all())
            ]
        ]);

        $itemDetail->update($data);

        if ($itemDetail->status == ItemDetailStatus::APPROVED && ! $itemDetail->is_notified) {
            try {
                $notifyWatchers = new NotifyWatchersOnPriceChange($itemDetail->fresh());
                $notifyWatchers->execute();

                $itemDetail->update([
                    'is_notified' => true
                ]);
            } catch (\Exception $e) {
                Log::warning("Notification Failed, {$e->getMessage()}");
            }
        }

        return $this->responseSuccess($itemDetail->fresh());
    }
}
