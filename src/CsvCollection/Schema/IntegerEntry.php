<?php

namespace StefanEnsmann\Laravel\CsvCollection\Schema;

use InvalidArgumentException;
use StefanEnsmann\Laravel\CsvCollection\Exceptions\SchemaValidationFailedException;

class IntegerEntry extends AbstractEntry
{
    public function parse(string $value): int
    {
        if (!is_numeric($value) || !ctype_digit(str_replace('-', '', $value))) {
            throw new SchemaValidationFailedException('This schema can only parse integers! Got ' . $value);
        }

        return floatval($value);
    }

    public function stringify($entry)
    {
        if (!is_int($entry)) {
            throw new InvalidArgumentException('This schema can only stringify integers!');
        }

        return strval($entry);
    }
}
