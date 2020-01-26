<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    protected static $repositories = [
        'base' => [
            \App\Repo\BaseRepositoryInterface::class,
            \App\Repo\BaseRepository::class,
        ],
        'user' => [
            \App\Repo\UserRepositoryInterface::class,
            \App\Repo\UserRepository::class,
        ],
        'password_reset_token' => [
            \App\Repo\PasswordResetTokenRepositoryInterface::class,
            \App\Repo\PasswordResetTokenRepository::class,
        ],
    ];
    
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        foreach (static::$repositories as $repository) {
            $this->app->singleton(
                $repository[0],
                $repository[1]
            );
        }
    }
}
