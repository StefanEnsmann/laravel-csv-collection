<?php

namespace StefanEnsmann\Laravel\CsvCollection\Schema;

use StefanEnsmann\Laravel\CsvCollection\Exceptions\SchemaValidationFailedException;

class SlugEntry extends AbstractEntry
{
    public function parse(string $value): string
    {
        if (!$this->isValid($value)) {
            throw new SchemaValidationFailedException('"' . $value . '" is not a valid slug');
        }

        return $value;
    }

    protected function isValid(string $value): bool
    {
        return preg_match('/^[a-z0-9-]+$/D', $value) > 0;
    }

    public function stringify($entry)
    {
        return strval($entry);
    }
}
