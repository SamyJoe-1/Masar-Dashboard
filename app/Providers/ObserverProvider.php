<?php

namespace App\Providers;

use App\Models\Applicant;
use App\Observers\ApplicantObserver;
use Illuminate\Support\ServiceProvider;
use App\Models\ApplicantForm;
use App\Observers\ApplicantFormObserver;

class ObserverProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ApplicantForm::observe(ApplicantFormObserver::class);
//        Applicant::observe(ApplicantObserver::class);
    }
}
