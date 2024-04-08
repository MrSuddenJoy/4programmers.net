<?php
namespace Neon\View\Language;

class English implements Language
{
    public function t(string $phrase): string
    {
        return $phrase;
    }

    public function dec(int $plurality, string $noun): string
    {
        return $noun;
    }
}
