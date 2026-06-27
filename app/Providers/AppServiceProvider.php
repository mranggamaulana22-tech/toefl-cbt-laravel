<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\QuestionRepositoryInterface;
use App\Repositories\QuestionRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(QuestionRepositoryInterface::class, QuestionRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
