<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL; // <--- 1. Importante: Agregamos esto

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
        // 2. Forzamos que todo se cargue con HTTPS
        // Esto arregla los estilos rotos cuando usas Ngrok
        // URL::forceScheme('https'); //Si para uso local comentar esta linea//
    }
}
