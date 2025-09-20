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
                    ->orWhere('applicants.id', $search)
                    ->orWhere('job_id', $search)
                    ->orWhere('file_id', $search);
            });
        })->when(@$filters['statuses'], function ($query, $statuses) {
            return $query->whereIn('status', $statuses);
        })->when(@$filters['status'], function ($query, $status) {
            return $query->where('status', $status);
        })->when(@$filters['user'], function ($query, $user) {
            return $query->where('job_apps.user_id', $user);
        });
    }
}
