<?php

namespace StefanEnsmann\Laravel\CsvCollection\Schema;

class StringEntry extends AbstractEntry
{
    public function parse(string $value): string
    {
        return $value;
    }

    public function stringify($entry)
    {
        return strval($entry);
    }
}
