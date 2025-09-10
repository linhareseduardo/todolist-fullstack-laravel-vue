<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;

class DateTimeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Configurar Carbon para usar o timezone do Brasil
        Carbon::setLocale('pt_BR');
        Carbon::setToStringFormat('d/m/Y H:i:s');
        
        // Definir timezone padrão para toda a aplicação
        if (config('app.timezone')) {
            date_default_timezone_set(config('app.timezone'));
        }
    }
}
