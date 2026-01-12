<?php

namespace StefanEnsmann\Laravel\CsvCollection\Schema;

use StefanEnsmann\Laravel\CsvCollection\Exceptions\SchemaValidationFailedException;

class UuidEntry extends AbstractEntry
{
    public function parse(string $value): string
    {
        if (preg_match('/^[\da-fA-F]{8}-[\da-fA-F]{4}-[\da-fA-F]{4}-[\da-fA-F]{4}-[\da-fA-F]{12}$/D', $value) > 0) {
            return $value;
        }

        throw new SchemaValidationFailedException('"' . $value . '" is not a valid UUID');
    }

    public function stringify($entry)
    {
        return strval($entry);
    }
}
