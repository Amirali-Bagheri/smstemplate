<?php

namespace Modules\SMS\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class SMSServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutes();

        $this->loadModuleMigrations();

    }

    protected function loadRoutes()
    {
        Route::prefix('api')
            ->middleware('api')


            ->group(base_path('Modules/SMS/Routes/api.php'));
//            ->group('../Routes/api.php');
    }

    /**
     * Load module migrations.
     *
     * @return void
     */
    protected function loadModuleMigrations()
    {
        $modulePath = base_path('Modules/SMS/Database/Migrations');

        $this->loadMigrationsFrom($modulePath);
    }

    public function register()
    {

    }
}
