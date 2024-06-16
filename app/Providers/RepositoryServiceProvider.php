<?php

namespace Coyote\Providers;

use Coyote\Repositories\Contracts\SessionRepositoryInterface;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Foundation\Application;

class RepositoryServiceProvider extends \Coyote\Providers\Neon\ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * @var array
     */
    private $provides = [];

    /**
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        parent::__construct($app);
        $files = (new Filesystem())->allFiles(app_path('Repositories/Contracts'));

        foreach ($files as $file) {
            $path = str_replace(app_path() . DIRECTORY_SEPARATOR, '', $file->getPathname());
            $this->provides[] = 'Coyote\\' . str_replace('/', '\\', substr($path, 0, -4));
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(
            array_pull($this->provides, array_search(SessionRepositoryInterface::class, $this->provides)),
            'Coyote\\Repositories\\Redis\\SessionRepository'
        );

        foreach ($this->provides as $interface) {
            $segments = explode('\\', $interface);
            $repository = substr((string) array_pop($segments), 0, -9);

            $this->app->singleton(
                $interface,
                implode('\\', array_merge(array_set($segments, 2, 'Eloquent'), [$repository]))
            );
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return $this->provides;
    }
}
