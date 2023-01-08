<?php

namespace App\Providers;

use App\Models\Channel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
//        Model::preventLazyLoading();
        Model::unguard();
        View::composer(['layouts.navigation', 'threads.create', 'threads.edit', 'threads.show'], fn($view) =>
        $view->with('channels', Cache::rememberForever('channels', fn() => Channel::latest()->get())));
    }
}
