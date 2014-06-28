<?php namespace Experience\Validation;

use Illuminate\Support\ServiceProvider;

class ValidationServiceProvider extends ServiceProvider
{
	protected $defer = false;

	public function register()
	{
        $this->app->bind(
            'Experience\Validation\Interfaces\ValidationProviderFactoryInterface',
            'Experience\Validation\Providers\LaravelValidationProviderFactory'
        );
	}

    public function boot()
    {
        $this->package('experience/validation');
    }
}
