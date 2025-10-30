<?php

namespace App\Providers;

use App\Repositories\Contracts\LeaderboardRepositoryInterface;
use App\Repositories\Contracts\PlayerRepositoryInterface;
use App\Repositories\MySQLPlayerRepository;
use App\Repositories\RedisLeaderboardRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(PlayerRepositoryInterface::class, MySQLPlayerRepository::class);
        $this->app->bind(LeaderboardRepositoryInterface::class, RedisLeaderboardRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
