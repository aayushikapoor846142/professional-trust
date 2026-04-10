<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use App\Models\StaffUser;
use Illuminate\Support\Facades\Auth;

class AddedByScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $userId = Auth::id();

        // Check if the logged-in user is a staff
        $professionalId = StaffUser::where('user_id', $userId)->value('added_by');

        if ($professionalId) {
            // Staff: show their own + their professional's records
            $builder->where(function ($q) use ($userId, $professionalId) {
                $q->where('added_by', $userId)
                  ->orWhere('added_by', $professionalId);
            });
        } else {
            // Professional: show their own + all their staff's records
            $staffIds = StaffUser::where('added_by', $userId)->pluck('user_id');

            $builder->where(function ($q) use ($userId, $staffIds) {
                $q->where('added_by', $userId);

                if ($staffIds->isNotEmpty()) {
                    $q->orWhereIn('added_by', $staffIds);
                }
            });
        }
    }
}
