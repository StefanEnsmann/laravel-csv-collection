<?php

namespace StefanEnsmann\Laravel\CsvCollection;

use InvalidArgumentException;
use Iterator;
use OutOfBoundsException;
use RangeException;
use StefanEnsmann\Laravel\CsvCollection\Array2D\ColumnAccessor;
use StefanEnsmann\Laravel\CsvCollection\Array2D\RowAccessor;

class Array2D implements Iterator
{
    private array $items = [];
    private int $currentIteratorRow = 0;

    public function __construct(
        private int $columns = 0
    ) {}

    /**
     * @return RowAccessor The current row
     */
    public function current(): mixed
    {
        return $this->valid() ? $this->getRow($this->currentIteratorRow) : null;
    }

    /**
     * @return int The current row index
     */
    public function key(): mixed
    {
        return $this->currentIteratorRow;
    }

    public function next(): void
    {
        $this->currentIteratorRow++;
    }

    public function rewind(): void
    {
        $this->currentIteratorRow = 0;
    }

    public function valid(): bool
    {
        return $this->currentIteratorRow >= 0 && $this->currentIteratorRow < $this->getRowCount();
    }

    public function getColumnCount(): int
    {
        return $this->columns;
    }

    public function getRowCount(): int
    {
        $itemCount = count($this->items);

        if ($itemCount < 1) {
            return 0;
        }

        return $itemCount / $this->columns;
    }

    public function insertColumn(mixed $default, int $index): void
    {
        if ($index < 0 || $index > $this->columns) {
            throw new RangeException('Column indices need to be between 0 and ' . $this->columns);
        }

        $itemCount = count($this->items);
        $this->columns++;

        if ($itemCount < 1) {
            return;
        }

        $rowCount = $itemCount / ($this->columns - 1);

        for ($i = 0; $i < $rowCount; $i++) {
            $rowStart = $i * $this->columns;
            array_splice($this->items, $rowStart + $index, 0, [$default]);
        }
    }

    public function addColumn(mixed $default): void
    {
        $this->insertColumn($default, $this->columns);
    }

    public function dropColumn(int $index): void
    {
        if ($index < 0 || $index >= $this->columns) {
            throw new RangeException('Column indices need to be between 0 and ' . ($this->columns - 1));
        }

        $itemCount = count($this->items);
        $this->columns--;

        if ($itemCount < 1) {
            return;
        }

        $rowCount = $itemCount / ($this->columns + 1);

        for ($i = $rowCount - 1; $i >= 0; $i--) {
            $rowStart = $i * ($this->columns + 1);
            array_splice($this->items, $rowStart + $index, 1);
        }
    }

    public function swapColumns(int $a, int $b): void
    {
        if ($a < 0 || $b < 0 || $a >= $this->columns || $b >= $this->columns) {
            throw new RangeException('Column indices need to be between 0 and ' . ($this->columns - 1));
        }

        if ($a === $b) {
            return;
        }

        $itemCount = count($this->items);

        if ($itemCount < 1) {
            return;
        }

        $rowCount = $itemCount / $this->columns;

        for ($i = 0; $i < $rowCount; $i++) {
            $rowStart = $i * $this->columns; $idxA = $rowStart + $a; $idxB = $rowStart + $b;

            $tmp = $this->items[$idxA];
            $this->items[$idxA] = $this->items[$idxB];
            $this->items[$idxB] = $tmp;
        }
    }

    public function insertRow(array $row, int $index): void
    {
        $rowSize = count($row);
        if ($rowSize !== $this->columns) {
            throw new InvalidArgumentException('Can not add a row with ' . $rowSize . ' entries. Expected ' . $this->columns);
        }

        if ($rowSize < 1) {
            return;
        }

        $rowCount = count($this->items) / $this->columns;

        if ($index < 0 || $index > $rowCount) {
            throw new RangeException('Row indices need to be between 0 and ' . $rowCount);
        }

        $rowStart = $index * $this->columns;

        array_splice($this->items, $rowStart, 0, $row);
    }

    public function addRow(array $row): void
    {
        $rowCount = count($this->items) / $this->columns;

        $this->insertRow($row, $rowCount);
    }

    public function dropRow(int $index): void
    {
        $rowCount = count($this->items) / $this->columns;

        if ($index < 0 || $index >= $rowCount) {
            throw new RangeException('Row indices need to be between 0 and ' . $rowCount);
        }

        $rowStart = $index * $this->columns;

        array_splice($this->items, $rowStart, $this->columns);
    }

    public function swapRows(int $a, int $b): void
    {
        $rowCount = count($this->items) / $this->columns;

        if ($a < 0 || $b < 0 || $a >= $rowCount || $b >= $rowCount) {
            throw new RangeException('Column indices need to be between 0 and ' . ($rowCount - 1));
        }

        if ($a === $b) {
            return;
        }

        $rowAStart = $a * $this->columns;
        $rowBStart = $b * $this->columns;

        for ($i = 0; $i < $this->columns; $i++) {
            $tmp = $this->items[$rowAStart + $i];
            $this->items[$rowAStart + $i] = $this->items[$rowBStart + $i];
            $this->items[$rowBStart + $i] = $tmp;
        }
    }

    private function calculateIndex(int $row, int $column): int
    {
        if ($column >= $this->columns) {
            throw new OutOfBoundsException(sprintf('%d is not a valid column index', $column));
        }

        $index = $row * $this->columns + $column;

        if ($index >= count($this->items)) {
            throw new OutOfBoundsException(sprintf('%d is not a valid row index', $row));
        }

        return $index;
    }

    public function getRow(int $row): RowAccessor
    {
        return new RowAccessor($this, $row);
    }

    public function getColumn(int $column): ColumnAccessor
    {
        return new ColumnAccessor($this, $column);
    }

    public function get(int $row, int $column): mixed
    {
        $index = $this->calculateIndex($row, $column);

        return $this->items[$index];
    }

    public function set(int $row, int $column, mixed $value = null): void
    {
        $index = $this->calculateIndex($row, $column);

        $this->items[$index] = $value;
    }
}
