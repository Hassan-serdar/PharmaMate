<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Feedback;
use App\Models\Medicine;
use App\Models\Pharmacy;
use App\Policies\UserPolicy;
use App\Policies\FeedbackPolicy;
use App\Policies\MedicinePolicy;
use App\Policies\PharmacyPolicy;
use App\Models\MedicineSuggestion;
use Illuminate\Support\Facades\Gate; 
use Illuminate\Support\ServiceProvider;
use App\Policies\MedicineSuggestionPolicy;

class AppServiceProvider extends ServiceProvider
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
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Feedback::class, FeedbackPolicy::class);
        Gate::policy(Medicine::class, MedicinePolicy::class);
        Gate::policy(Pharmacy::class, PharmacyPolicy::class);
        Gate::policy(MedicineSuggestion::class, MedicineSuggestionPolicy::class);

    }
}
