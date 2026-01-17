<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Models\Ninja;
use App\Models\Avatar;
use App\Models\DragonBall;

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
        Relation::morphMap([
        'ninja'     => Ninja::class,
        'avatar'      => Avatar::class,
        'dragon_ball' => DragonBall::class,
    ]);
    }
}
