<?php

namespace App\Providers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * Automatically migrate the in-memory SQLite database when running the
     * built-in development server so that the :memory: store survives across
     * requests in the single CLI-server process.
     */
    public function boot(): void
    {
        JsonResource::withoutWrapping();

        $connection = config('database.default');
        $database = config("database.connections.{$connection}.database");

        if ($connection === 'sqlite' && $database === ':memory:' && ! $this->app->runningInConsole()) {
            Artisan::call('migrate', ['--force' => true]);
        }
    }
}
