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
        try {
            return Carbon::parse($value);
        } catch (InvalidFormatException $e) {
            throw new SchemaValidationFailedException('This schema can only parse dates! Got ' . $value, 0, $e);
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
