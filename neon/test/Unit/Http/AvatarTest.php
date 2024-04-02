<?php
namespace Neon\Test\Unit\Http;

use Coyote\User;
use Illuminate\Auth\AuthManager;
use Neon\Test\BaseFixture;
use Neon\Test\BaseFixture\Selector\Selector;
use Neon\Test\BaseFixture\View\ViewDom;
use PHPUnit\Framework\TestCase;
use Tests\Unit\BaseFixture\Server;

class AvatarTest extends TestCase
{
    use Server\Laravel\Application;
    use Server\Http;
    use BaseFixture\PublicImageUrl;

    /**
     * @test
     */
    public function guestAvatar(): void
    {
        $this->assertSame(
            '/neon/avatarPlaceholder.png',
            $this->renderedAvatarUrl());
    }

    /**
     * @test
     */
    public function loggedIn(): void
    {
        $this->publicImageBaseUrl('http://cdn.com/public');
        $this->loggedInUserWithAvatar('foo.png');
        $this->assertSame(
            'http://cdn.com/public/foo.png',
            $this->renderedAvatarUrl());
    }

    private function loggedInUserWithAvatar(string $avatarUrl): User
    {
        $user = new User();
        $user->name = \uniqid();
        $user->email = '';
        $user->photo->setFilename($avatarUrl);
        /** @var AuthManager $auth */
        $auth = $this->laravel->app->get(AuthManager::class);
        $auth->login($user);
        return $user;
    }

    private function renderedAvatarUrl(): string
    {
        $dom = new ViewDom($this->htmlView('/events'));
        $selector = new Selector('header', '#userAvatar', '@src');
        return $dom->find($selector->xPath());
    }

    private function htmlView(string $uri): string
    {
        return $this->server->get($uri)
            ->assertSuccessful()
            ->getContent();
    }
}