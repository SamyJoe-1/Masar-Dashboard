<?php

namespace App\Traits\Job;

trait queryScope
{
    public function scopeWithCounts($query)
    {
        return $query->withCount('applicants')->withCount('approved_applicants')->withCount('rejected_applicants');
    }

    public function scopeFilter($query, array $filters)
    {
        return $query->when(@$filters['search'], function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('title', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%")
                    ->orWhere('id', $search);
            });
        })->when(@$filters['organizations'], function ($query, $organizations) {
            return $query->where('organization_id', $organizations);
        })->when(array_key_exists('target', $filters), function ($query) use ($filters) {
            if ($filters['target'] != 2){
                $query->where('target', $filters['target']);
            }
        });
    }
}
