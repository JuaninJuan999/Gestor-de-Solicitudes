<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon; // <--- NUEVO: Agregamos Carbon

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
        // Forzamos que todo se cargue con HTTPS
        // Esto arregla los estilos rotos cuando usas Ngrok
        // URL::forceScheme('https'); //Si para uso local comentar esta linea//

        // Rutas y formularios usan APP_URL; si entras por otra IP/puerto (ej. LAN),
        // los enlaces apuntaban al host equivocado y parecía que "no hacían nada".
        if (! $this->app->runningInConsole()) {
            $configuredRoot = rtrim((string) config('app.url'), '/');
            $currentRoot = rtrim(request()->getSchemeAndHttpHost().request()->getBaseUrl(), '/');
            if ($configuredRoot !== '' && $currentRoot !== '' && $configuredRoot !== $currentRoot) {
                URL::forceRootUrl($currentRoot);
            }
        }

        // NUEVO: Configurar Carbon en español para fechas relativas
        Carbon::setLocale('es');
    }
}
