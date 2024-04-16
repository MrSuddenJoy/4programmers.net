<?php
namespace Xenon;

readonly class If_ implements ViewItem
{
    private Fragment $conditionBody;

    public function __construct(private string $conditionField, array $body)
    {
        $this->conditionBody = new Fragment($body);
    }

    public function ssrHtml(array $state): string
    {
        if ($state[$this->conditionField]) {
            return $this->conditionBody->ssrHtml($state);
        }
        return '';
    }

    public function spaNode(): string
    {
        return "store.$this->conditionField ? {$this->conditionBody->spaExpression()} : []";
    }
}
