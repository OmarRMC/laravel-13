<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
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
     */
    public function boot(): void
    {
        $this->definirGates();
    }

    private function definirGates(): void
    {
        /** /panel — organizadores y administradores. */
        Gate::define('crear eventos', function (User $user): bool {
            return $user->tieneRol(['organizador', 'admin']);
        });

        /** /admin — solo administradores. */
        Gate::define('ver-panel-admin', function (User $user): bool {
            return $user->tieneRol('admin');
        });
    }
}
