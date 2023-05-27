<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->registerModuleServiceProviders();
    }

    protected function registerModuleServiceProviders(): void
    {
        $modulePath = base_path('Modules');

        if (File::isDirectory($modulePath)) {
            $modules = File::directories($modulePath);

            foreach ($modules as $module) {
                $serviceProvider = $module  . '/Providers/' . basename($module) . 'ServiceProvider.php';

                if (File::exists($serviceProvider)) {
                    $this->app->register("Modules\\" . basename($module) . "\\Providers\\" . basename($module) . 'ServiceProvider');
                }
            }
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
    }
}
