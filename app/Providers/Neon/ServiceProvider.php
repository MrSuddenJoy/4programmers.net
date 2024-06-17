<?php
namespace Coyote\Providers\Neon;

use Illuminate\Database\DatabaseManager;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Session\Middleware\StartSession;
use Neon\Application;
use Neon\Laravel;
use Neon\Laravel\JobOffers;
use Neon\Laravel\LaravelVisitor;
use Neon\Persistence;
use Neon\StaticEvents;

class ServiceProvider extends \Coyote\Providers\RouteServiceProvider
{
    public function register(): void
    {
        parent::register();
        $this->app->instance(
            Application::class,
            new Application('4programmers.net',
                $this->attendance(),
                new JobOffers(),
                new StaticEvents(),
                new LaravelVisitor($this->app),
                new Laravel\CoyoteSystem($this->app),
            ));
    }

    public function loadRoutes(): void
    {
        $this
            ->get('/events', [
                'uses' => function (Request $request) {
                    /** @var Application $application */
                    $application = $this->app->get(Application::class);
                    return $application->html($this->startSessionGetCsrf($request));
                },
            ])
            ->middleware('neon');
    }

    private function attendance(): Persistence\Attendance
    {
        /** @var DatabaseManager $database */
        $database = $this->app->get(DatabaseManager::class);
        return new Laravel\Attendance($database);
    }

    private function startSessionGetCsrf(Request $request): string
    {
        /** @var StartSession $middleware */
        $middleware = $this->app->get(StartSession::class);
        $middleware->handle($request, fn() => new Response(''));
        return session()->token() ?? '';
    }
}
