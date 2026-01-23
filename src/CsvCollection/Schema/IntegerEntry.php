<?php

namespace StefanEnsmann\Laravel\CsvCollection\Schema;

use InvalidArgumentException;
use StefanEnsmann\Laravel\CsvCollection\Exceptions\SchemaValidationFailedException;

class IntegerEntry extends AbstractEntry
{
    public function parse(string $value): int
    {
        if (!$this->isValid($value)) {
            throw new SchemaValidationFailedException('This schema can only parse integers! Got ' . $value);
        }

        return intval($value);
    }

    public function isValid(string $value): bool
    {
        return is_numeric($value) && ctype_digit(str_replace('-', '', $value));
    }

    public function stringify($entry)
    {
        if (!is_int($entry)) {
            throw new InvalidArgumentException('This schema can only stringify integers!');
        }

        return strval($entry);
    }
}
