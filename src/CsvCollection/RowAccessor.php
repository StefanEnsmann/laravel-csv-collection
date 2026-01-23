<?php

namespace StefanEnsmann\Laravel\CsvCollection;

use StefanEnsmann\Laravel\CsvCollection;
use StefanEnsmann\Laravel\CsvCollection\Array2D\RowAccessor as Array2DRowAccessor;

class RowAccessor extends Array2DRowAccessor
{
    public function __construct(
        protected CsvCollection $collection,
        protected int $index,
    ) {
        parent::__construct($collection, $index);
    }

    public function offsetExists(mixed $offset): bool
    {
        return parent::offsetExists($this->collection->keyToIndex($offset));
    }

    public function offsetGet(mixed $offset): mixed
    {
        return parent::offsetGet($this->collection->keyToIndex($offset));
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        parent::offsetSet($this->collection->keyToIndex($offset), $value);
    }
}
