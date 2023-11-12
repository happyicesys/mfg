<?php

namespace App\Providers;

// use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
// use Laravel\Scout\Builder;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $profile = \App\Models\Profile::with('profileSetting')->where('is_primary', 1)->first();

        if($profile) {
            View::share('profile', $profile);
        }

        // Builder::macro('search', function($field, $string) {
        //     return $string ? $this->where($field, 'like', '%'.$string.'%') : $this;
        // });
    }
}
