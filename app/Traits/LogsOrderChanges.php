<?php

namespace App\Traits;

use App\Models\OrderChange;
use Illuminate\Support\Facades\Auth;

trait LogsOrderChanges
{
    /**
     * Boot the trait.
     */
    public static function bootLogsOrderChanges()
    {
        static::updating(function ($model) {
            $changedAttributes = $model->getDirty(); // Get all changed attributes
            $groupedChanges = [];

            // Log each attribute change in this transaction
            foreach ($changedAttributes as $attribute => $newValue) {
                $originalValue = $model->getOriginal($attribute);

                // Collect the changes into a grouped array
                $groupedChanges[] = [
                    'attribute' => $attribute,
                    'original_value' => $originalValue,
                    'new_value' => $newValue
                ];
            }

            // Determine who made the change
            $changedBy = Auth::check() ? Auth::user()->email : 'Webhook';

            // Log the grouped changes
            OrderChange::create([
                'order_id' => $model->id,
                'changes' => json_encode($groupedChanges),  // Store the grouped changes in JSON
                'changed_by' => $changedBy
            ]);
        });
    }
}
