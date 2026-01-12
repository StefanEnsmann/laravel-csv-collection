<?php

namespace StefanEnsmann\Laravel\CsvCollection\Schema;

use StefanEnsmann\Laravel\CsvCollection\Exceptions\SchemaValidationFailedExceptionException;

/**
 * @template T
 */
abstract class AbstractEntry
{
    /**
     * @param string $value The string value coming from the CSV file
     * 
     * @return T The parsed value
     * 
     * @throws SchemaValidationFailedExceptionException
     */
    abstract public function parse(string $value);

    /**
     * @param T $entry The value to stringify
     * 
     * @return string The string representation for storing in a CSV file
     */
    abstract public function stringify($entry);
}
