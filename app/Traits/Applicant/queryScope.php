<?php

namespace App\Traits\Applicant;

trait queryScope
{
    public function scopeWithCounts($query)
    {
        return $query->withCount('form');
    }

    public function scopeFilter($query, array $filters)
    {
        return $query->when(@$filters['search'], function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('status', 'like', "%$search%")
                    ->orWhere('information', 'like', "%$search%")
                    ->orWhere('id', $search)
                    ->orWhere('job_id', $search)
                    ->orWhere('file_id', $search);
            });
        });
    }
}
