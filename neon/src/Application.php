<?php
namespace Neon;

use Neon\View\Html\Attendance;
use Neon\View\Html\Head\Favicon;
use Neon\View\Html\Head\Title;
use Neon\View\Html\Item;
use Neon\View\Html\Navigation;
use Neon\View\Html\Section;
use Neon\View\Html\UntypedItem;

readonly class Application
{
    public function __construct(private string $applicationName)
    {
    }

    public function html(): string
    {
        $view = new View\HtmlView([
            new Title($this->applicationName),
            new Favicon('https://4programmers.net/img/favicon.png'),
        ],
            [
                new Navigation(new View\ViewModel\Navigation(
                    [
                        'Forum'      => '/Forum',
                        'Microblogs' => '/Mikroblogi',
                        'Jobs'       => '/Praca',
                        'Wiki'       => '/Kategorie',
                    ],
                    'https://github.com/pradoslaw/coyote',
                    'https://github.com/pradoslaw/coyote/stargazers',
                    'Coyote',
                    '111',
                    [
                        'Create account' => '/Register',
                        'Login'          => '/Login',
                    ],
                )),
                $this->asideMain(
                    new Attendance(
                        '116.408', '124',
                        'Users', 'Online'),
                    new Section(
                        $this->applicationName,
                        'Incoming events',
                        $this->events())),
            ]);
        return $view->html();
    }

    private function asideMain(Item $aside, Item $main): Item
    {
        return new UntypedItem(fn(callable $h): array => [
            $h('div', [
                $h('aside', $aside->html($h), 'lg:w-1/4 lg:pr-2 mb-4 lg:mb-0'),
                $h('main', $main->html($h), 'lg:w-3/4 lg:pl-2'),
            ], 'lg:flex container mx-auto'),
        ]);
    }

    private function events(): array
    {
        $_4developers = new View\Html\Event(
            new View\ViewModel\Event(new \Neon\Domain\Event(
                '4DEVELOPERS',
                'Warszawa',
                false,
                ['Software', 'Hardware'],
                new \Neon\Domain\Date(2024, 4, 16),
                \Neon\Domain\EventKind::Conference,
            )));
        $foundersMind = new View\Html\Event(
            new View\ViewModel\Event(new \Neon\Domain\Event(
                'Founders Mind VII',
                'Warszawa',
                false,
                ['Biznes', 'Networking'],
                new \Neon\Domain\Date(2024, 5, 14),
                \Neon\Domain\EventKind::Conference,
            )));
        $hackingLeague = new View\Html\Event(
            new View\ViewModel\Event(new \Neon\Domain\Event(
                'Best Hacking League',
                'Warszawa',
                true,
                ['Software', 'Hardware', 'AI', 'Cybersecurity'],
                new \Neon\Domain\Date(2024, 4, 20),
                \Neon\Domain\EventKind::Hackaton,
            )));

        return [
            $_4developers,
            $hackingLeague,
            $foundersMind,
        ];
    }
}
