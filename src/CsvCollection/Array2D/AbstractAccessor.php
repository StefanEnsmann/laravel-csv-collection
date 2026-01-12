<?php

namespace StefanEnsmann\Laravel\CsvCollection\Array2D;

use ArrayAccess;
use BadFunctionCallException;
use OutOfBoundsException;
use StefanEnsmann\Laravel\CsvCollection\Array2D;

abstract class AbstractAccessor implements ArrayAccess
{
    public function __construct(
        protected Array2D $array,
        protected int $index,
    ) {}

    abstract protected function indexExistsInArray(): bool;

    protected function ensureIndexExistsInArray(): void
    {
        if (!$this->indexExistsInArray()) {
            throw new OutOfBoundsException('The requested index not is not available');
        }
    }

    public function offsetUnset(mixed $offset): void
    {
        throw new BadFunctionCallException('Unsetting individual cells is not permitted');
    }
}
