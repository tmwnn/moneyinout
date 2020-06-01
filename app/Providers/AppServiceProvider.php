<?php

namespace App\Providers;

use App\Services\Categories\Repositories\CachedCategoryRepository;
use App\Services\Categories\Repositories\CachedCategoryRepositoryInterface;
use App\Services\Categories\Repositories\CategoryRepositoryInterface;
use App\Services\Categories\Repositories\EloquentCategoryRepository;
use App\Services\Operations\Repositories\CachedOperationRepository;
use App\Services\Operations\Repositories\CachedOperationRepositoryInterface;
use App\Services\Operations\Repositories\EloquentOperationRepository;
use App\Services\Operations\Repositories\OperationRepositoryInterface;
use App\Services\Users\Repositories\UserRepositoryInterface;
use App\Services\Users\Repositories\EloquentUserRepository;
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
        $this->app->bind(CategoryRepositoryInterface::class, EloquentCategoryRepository::class);
        $this->app->bind(UserRepositoryInterface::class, EloquentUserRepository::class);
        $this->app->bind(OperationRepositoryInterface::class, EloquentOperationRepository::class);
        $this->app->bind(CachedCategoryRepositoryInterface::class, CachedCategoryRepository::class);
        $this->app->bind(CachedOperationRepositoryInterface::class, CachedOperationRepository::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
