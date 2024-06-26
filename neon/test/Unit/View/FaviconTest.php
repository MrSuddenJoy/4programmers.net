<?php
namespace Neon\Test\Unit\View;

use Neon\Test\BaseFixture\View\ViewDom;
use Neon\View\Html\Head\Favicon;
use Neon\View\HtmlView;
use PHPUnit\Framework\TestCase;

class FaviconTest extends TestCase
{
    /**
     * @test
     */
    public function test(): void
    {
        $view = new HtmlView([new Favicon('https://host/favicon.png')],
            [], false);

        $this->assertSame(
            '<link rel="shortcut icon" href="https://host/favicon.png" type="image/png">',
            $this->favicon($view));
    }

    private function favicon(HtmlView $view): string
    {
        $dom = new ViewDom($view->html());
        return $dom->html('/html/head/link[@rel="shortcut icon"]');
    }
}
