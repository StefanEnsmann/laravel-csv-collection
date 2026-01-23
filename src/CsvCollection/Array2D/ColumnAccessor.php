<?php

namespace StefanEnsmann\Laravel\CsvCollection\Array2D;

class ColumnAccessor extends AbstractAccessor
{
    protected function indexExistsInArray(): bool
    {
        return $this->index >= 0 && $this->index < $this->array->getColumnCount();
    }

    public function offsetExists(mixed $offset): bool
    {
        $this->ensureIndexExistsInArray();

        return $offset >= 0 && $offset < $this->array->getRowCount();
    }

    public function offsetGet(mixed $offset): mixed
    {
        $this->ensureIndexExistsInArray();

        return $this->array->get($offset, $this->index);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->ensureIndexExistsInArray();

        $this->array->get($offset, $this->index, $value);
    }
}
