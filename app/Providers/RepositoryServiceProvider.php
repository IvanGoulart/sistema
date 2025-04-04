<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\Schedule\ScheduleRepositoryInterface;
use App\Interfaces\PermissionRepositoryInterface;

use App\Repositories\UserRepository;
use App\Repositories\ScheduleRepository;
use App\Repositories\PermissionRepository;

class RepositoryServiceProvider extends ServiceProvider
{
  /**
   * Register services.
   */
  public function register(): void
  {
    $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
    $this->app->bind(PermissionRepositoryInterface::class, PermissionRepository::class);
    $this->app->bind(ScheduleRepositoryInterface::class, ScheduleRepository::class);
  }

  /**
   * Bootstrap services.
   */
  public function boot(): void
  {
    //
  }
}
