<?php

namespace StefanEnsmann\Laravel\CsvCollection\Schema;

class StringEntry extends AbstractEntry
{
    public function parse(string $value): string
    {
        return $value;
    }

    protected function isValid(string $value): bool
    {
        return true;
    }

    public function stringify($entry)
    {
        return strval($entry);
    }
}
