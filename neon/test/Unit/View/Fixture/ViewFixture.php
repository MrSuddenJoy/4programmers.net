<?php
namespace Neon\Test\Unit\View\Fixture;

use Neon;
use Neon\Test\BaseFixture\View\ViewDom;

trait ViewFixture
{
    function view(array $fields): Neon\View\HtmlView
    {
        return new Neon\View\HtmlView([], [
            new Neon\View\Html\Section(
                $fields['root'] ?? '',
                $fields['sectionTitle'] ?? '',
                []),
        ]);
    }

    function text(Neon\View\HtmlView $view, string $xPath): string
    {
        $dom = new ViewDom($view->html());
        return $dom->find($xPath);
    }

    function texts(Neon\View\HtmlView $view, string $xPath): array
    {
        $dom = new ViewDom($view->html());
        return $dom->findMany($xPath);
    }
}
