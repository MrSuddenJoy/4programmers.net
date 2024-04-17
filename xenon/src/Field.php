<?php
namespace Xenon;

readonly class Field implements ViewItem
{
    private FieldName $field;

    public function __construct(string $fieldName)
    {
        $this->field = new FieldName($fieldName);
    }

    public function ssrHtml(array $state): string
    {
        return \htmlSpecialChars($this->field->ssrValue($state));
    }

    public function spaNode(): string
    {
        return $this->field->spaVariable;
    }
}