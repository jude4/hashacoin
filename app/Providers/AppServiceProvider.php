<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use App\Models\Settings;
use App\Models\Logo;
use App\Models\User;
use Illuminate\Support\Facades\View;
use Illuminate\Pagination\Paginator;

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
        // Paginator::useBootstrap();
        // View::composer('*', function($view){
        //     if (Auth::guard('admin')->check()) {
        //         $view->with('admin', Admin::find(Auth::guard('admin')->user()->id));
        //     }
        //     if (Auth::guard('user')->check()) {
        //         $view->with('user', User::find(Auth::guard('user')->user()->id));
        //     }
        //     $view->with('set', Settings::first());
        //     $view->with('logo', Logo::first());
        //     if(url()->current()!=route('ipn.flutter')){
        //         sub_check();
        //     }
        // });
    }
}
