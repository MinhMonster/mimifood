<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * @var string
     */
    public const HOME = '/home';

    public function boot()
    {
        $this->configureRateLimiting();

        /**
         * =========================
         * Admin Game CRUD Route Macro
         * =========================
         */
        Route::macro('adminGameCrud', function (
            string $prefix,
            string $controller,
            string $namePrefix = null
        ) {
            $namePrefix ??= 'admin.' . str_replace('-', '.', $prefix) . '.';

            Route::prefix($prefix)
                ->name($namePrefix)
                ->controller($controller)
                ->group(function () {
                    Route::get('/', 'index')->name('index');
                    Route::get('{id}', 'show')->name('show');

                    // create + update (gộp)
                    Route::post('modify', 'modify')->name('modify');

                    // soft delete
                    Route::post('{id}/destroy', 'destroy')
                        ->withTrashed()
                        ->name('destroy');

                    // restore
                    Route::post('{id}/restore', 'restore')
                        ->withTrashed()
                        ->name('restore');
                });
        });

        /**
         * =========================
         * Load Routes
         * =========================
         */
        $this->routes(function () {
            Route::middleware('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)
                ->by(optional($request->user())->id ?: $request->ip());
        });
    }
}
