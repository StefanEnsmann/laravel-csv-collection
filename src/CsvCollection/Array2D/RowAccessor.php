<?php

namespace StefanEnsmann\Laravel\CsvCollection\Array2D;

class RowAccessor extends AbstractAccessor
{
    protected function indexExistsInArray(): bool
    {
        return $this->index >= 0 && $this->index < $this->array->getRowCount();
    }

    public function offsetExists(mixed $offset): bool
    {
        $this->ensureIndexExistsInArray();

        return $offset >= 0 && $offset < $this->array->getColumnCount();
    }

    public function offsetGet(mixed $offset): mixed
    {
        $this->ensureIndexExistsInArray();

        return $this->array->get($this->index, $offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->ensureIndexExistsInArray();

        $this->array->get($this->index, $offset, $value);
    }
}
