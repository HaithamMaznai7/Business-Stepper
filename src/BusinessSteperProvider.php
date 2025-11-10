<?php 

namespace haimaz\BusinessSteper;

use Illuminate\Support\ServiceProvider;

class BusinessSteperProvider extends ServiceProvider
{

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/business_steper.php',
            'business_steper'
        );        
    } 

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {        
        // Allow users to publish the config to their app/config directory
        $this->publishes([
            __DIR__.'/../config/business_steper.php' => config_path('business_steper.php'),
        ], 'business-services-config');

        // Optionally, publish them to the app's database/migrations directory
        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'business-services-migrations');

        // Let Laravel know where your package’s migrations are
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }
}