<?php
namespace Neon\Test\Unit\Http;

use Neon\Application;
use PHPUnit\Framework\TestCase;
use Tests\Unit\BaseFixture;

class Test extends TestCase
{
    use BaseFixture\Server\Laravel\Application;
    use BaseFixture\Server\Http;

    /**
     * @test
     */
    public function http(): void
    {
        $this->assertRendersView('/events');
    }

    private function assertRendersView(string $uri): void
    {
        $this->laravel->app->instance(Application::class, new Application('Ours is the fury'));
        $response = $this->server->get($uri)
            ->assertSuccessful()
            ->getContent();
        $this->assertStringContainsString(
            '<title>Ours is the fury</title>',
            $response);
    }
}