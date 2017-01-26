<?php
namespace JsLocalization;

use App;
use Artisan;
use Config;
use View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;
use JsLocalization\Console\RefreshCommand;

class JsLocalizationServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
        $this->publishes([
            __DIR__.'/../config/config.php' => config_path('js-localization.php')
        ]);
        
        $this->mergeConfigFrom(
            __DIR__.'/../config/config.php', 'js-localization'
        );
        
		$this->loadViewsFrom(__DIR__.'/../resources/views', 'js-localization');
        
        $this->registerRefreshCommand();
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		require __DIR__.'/bindings.php';
		require __DIR__.'/Http/routes.php';
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return ['js-localization'];
	}

	/**
	 * Register js-localization.refresh
	 */
	private function registerRefreshCommand()
	{
		$this->app->singleton('js-localization.refresh', function()
		{
			return new RefreshCommand;
		});

		$this->commands('js-localization.refresh');
	}

}
