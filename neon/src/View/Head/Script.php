<?php
namespace Neon\View\Head;

readonly class Script implements Head
{
    public function __construct(private string $url)
    {
    }

    public function headHtml(callable $h): string
    {
        return $h('script', [], ['src' => $this->url]);
    }
}
