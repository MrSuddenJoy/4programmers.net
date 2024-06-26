<?php
namespace Neon\View\Html;

class UntypedItem implements Item
{
    /** @var callable */
    private $children;

    public function __construct(callable $children)
    {
        $this->children = $children;
    }

    public function render(Render $h): array
    {
        return ($this->children)($h);
    }
}
