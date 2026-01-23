<?php

namespace StefanEnsmann\Laravel\CsvCollection\Schema;

use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use DateTimeInterface;
use InvalidArgumentException;
use StefanEnsmann\Laravel\CsvCollection\Exceptions\SchemaValidationFailedException;

class DateEntry extends AbstractEntry
{
    public function parse(string $value): DateTimeInterface
    {
        if (!$this->isValid($value)) {
            throw new SchemaValidationFailedException('This schema can only parse dates! Got ' . $value);
        }

        return Carbon::parse($value);
    }

    public function isValid(string $value): bool
    {
        try {
            Carbon::parse($value);

            return true;
        } catch (InvalidFormatException) {
            return false;
        }
    }

    public function stringify($entry)
    {
        if (!$entry instanceof DateTimeInterface) {
            throw new InvalidArgumentException('This schema can only stringify dates!');
        }

        return $entry->format('Y/m/d');
    }
}
