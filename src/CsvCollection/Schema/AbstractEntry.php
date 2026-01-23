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
     * @param string $value The string value to be validated
     * 
     * @return bool If the passed string complies with this schema entry
     */
    abstract protected function isValid(string $value): bool;

    /**
     * @param T $entry The value to stringify
     * 
     * @return string The string representation for storing in a CSV file
     */
    abstract public function stringify($entry);
}
