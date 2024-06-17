<?php

namespace Coyote\Providers;

use Coyote\Services\Sitemap\Sitemap;
use Illuminate\Support\ServiceProvider;

class SitemapProvider extends \Coyote\Providers\Neon\ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('sitemap', function ($app) {
            return new Sitemap($app['filesystem']->disk('local'));
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['sitemap'];
    }
}
