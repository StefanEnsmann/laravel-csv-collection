<?php

namespace StefanEnsmann\Laravel\CsvCollection\Schema;

class PipedArrayEntry extends AbstractEntry
{
    public function parse(string $value): array
    {
        if (empty($value)) {
            return [];
        }

        $entry = explode('|', $value);

        return array_map(fn (string $e) => trim($e), $entry);
    }

    public function stringify($entry)
    {
        return strval($entry);
    }
}
