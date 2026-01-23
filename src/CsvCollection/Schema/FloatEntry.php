<?php

namespace StefanEnsmann\Laravel\CsvCollection\Schema;

use InvalidArgumentException;
use StefanEnsmann\Laravel\CsvCollection\Exceptions\SchemaValidationFailedException;

class FloatEntry extends AbstractEntry
{
    public function parse(string $value): float
    {
        if (!$this->isValid($value)) {
            throw new SchemaValidationFailedException('This schema can only parse numbers! Got ' . $value);
        }

        return floatval($value);
    }

    public function isValid(string $value): bool
    {
        return is_numeric($value);
    }

    public function stringify($entry)
    {
        if (!is_int($entry) && !is_float($entry)) {
            throw new InvalidArgumentException('This schema can only stringify numbers!');
        }

        return strval($entry);
    }
}
