<?php

namespace StefanEnsmann\Laravel;

use InvalidArgumentException;
use StefanEnsmann\Laravel\CsvCollection\Array2D;
use StefanEnsmann\Laravel\CsvCollection\Exceptions\InvalidCsvFormatException;
use StefanEnsmann\Laravel\CsvCollection\RowAccessor;
use StefanEnsmann\Laravel\CsvCollection\Schema\AbstractEntry;
use StefanEnsmann\Laravel\CsvCollection\Schema\StringEntry;

class CsvCollection extends Array2D
{
    private ?array $header = null;

    public const USE_HEADER = 0;
    public const OMIT_HEADER = 1;

    public function __construct(array $schema)
    {
        parent::__construct(count($schema));

        if (self::isMap($schema)) {
            $this->header = array_keys($schema);
        } else if (!array_is_list($schema)) {
            throw new InvalidArgumentException('Provided schema is neither a list nor has only string keys');
        } // else: keep null a header
    }

    private static function isMap(array $array): bool
    {
        foreach ($array as $key => $_) {
            if (!is_string($key)) {
                return false;
            }
        }

        return true;
    }

    public static function read(
        string $path,
        int|array $schema = self::USE_HEADER,
        ?int $maxLineLength = null,
        string $separator = ',',
        string $enclosure = '"',
        string $escape = '\\',
    ): static {
        $handle = fopen($path, 'r');

        if ($handle === false) {
            throw new InvalidArgumentException('Can not open file ' . $path);
        };

        try {
            $firstLine = self::readUntilNotEmpty($handle, $maxLineLength, $separator, $enclosure, $escape);
            
            if ($firstLine === false) {
                throw new InvalidCsvFormatException('Could not detect schema from CSV file!');
            } else if ($schema === self::USE_HEADER) {
                $schema = array_fill_keys($firstLine, StringEntry::class);
            } else if ($schema === self::OMIT_HEADER) {
                $schema = array_fill(0, count($firstLine), StringEntry::class);
                rewind($handle); // read the first line again in the processing loop
            } else if (!is_array($schema)) {
                throw new InvalidArgumentException('Schema must be an array or USE_HEADER or OMIT_HEADER!');
            }

            $instance = new static($schema);
            $keys = array_keys($schema);

            $parsers = [];
            
            while (($data = self::readUntilNotEmpty($handle, $maxLineLength, $separator, $enclosure, $escape)) !== false) {
                if (count($data) !== $instance->getColumnCount()) {
                    throw new InvalidCsvFormatException('Column count does not match schema!');
                }

                $row = [];
                foreach ($data as $index => $value) {
                    $parserClass = $schema[$keys[$index]];
                    /** @var AbstractEntry $parser */
                    $parser = $parsers[$parserClass] ??= new $parserClass;
                    $row[$index] = $parser->parse($value);
                }

                $instance->addRow($row);
            }

            return $instance;
        } finally {
            fclose($handle);
        }
    }

    private static function readUntilNotEmpty(
        $handle,
        ?int $length = null,
        string $separator = ',',
        string $enclosure = '"',
        string $escape = '\\',
    ): array|false {
        $data = null;
        while (is_null($data) || (is_array($data) && $data[0] === null)) {
            $data = fgetcsv($handle, $length, $separator, $enclosure, $escape);
        }

        return $data;
    }

    public function hasHeader(): bool
    {
        return !is_null($this->header);
    }

    public function keyToIndex(int|string $key): int
    {
        if (is_int($key)) {
            return $key;
        }

        /** @var int|false $index */
        $index = array_search($key, $this->header);

        if ($index === false) {
            throw new InvalidArgumentException(sprintf('Header "%s" is not present in this CSV instance', $index));
        }

        return $index;
    }

    private function unpackRow(array $row): array
    {
        if (array_is_list($row)) {
            return $row;
        } else if (!self::isMap($row)) {
            throw new InvalidArgumentException('Can not mix anonymous with named CSV columns');
        }

        $keys = array_keys($row);

        $missingHeaders = array_diff($this->header, $keys);
        if (!empty($missingHeaders)) {
            throw new InvalidArgumentException(sprintf('Headers missing from row: %s', implode(', ', $missingHeaders)));
        }

        $additionalHeaders = array_diff($keys, $this->header);
        if (!empty($additionalHeaders)) {
            throw new InvalidArgumentException(sprintf('Additional headers on row: %s', implode(', ', $additionalHeaders)));
        }

        $newRow = [];
        foreach ($this->header as $header) {
            $newRow[] = $row[$header];
        }

        return $newRow;
    }

    public function insertColumn(mixed $default, int $index, ?string $header = null): void
    {
        $header = empty($header) ? null : $header;

        if (!is_null($header) && !$this->hasHeader()) {
            throw new InvalidArgumentException('Header columns can only be added to CSV instances with a header structure');
        }

        if (is_null($header) && $this->hasHeader()) {
            throw new InvalidArgumentException('CSV instances with a header can not contain anonymous columns');
        }

        if ($header && in_array($header, $this->header)) {
            throw new InvalidArgumentException(sprintf('Header key %s is already present', $header));
        }

        parent::insertColumn($default, $index);

        if ($header) {
            array_splice($this->header, $index, 0, $header);
        }
    }

    public function addColumn(mixed $default, ?string $header = null): void
    {
        $this->insertColumn($default, $this->getColumnCount(), $header);
    }

    public function dropColumn(int|string $index): void
    {
        $indexToRemove = $this->keyToIndex($index);

        parent::dropColumn($indexToRemove);

        array_splice($this->header, $indexToRemove, 1);
    }

    public function swapColumns(int|string $a, int|string $b): void
    {
        $a = $this->keyToIndex($a);
        $b = $this->keyToIndex($b);

        if ($a === $b) {
            return;
        }

        parent::swapColumns($a, $b);

        $tmp = $this->header[$a];
        $this->header[$a] = $this->header[$b];
        $this->header[$b] = $tmp;
    }

    public function insertRow(array $row, int $index): void
    {
        parent::insertRow($this->unpackRow($row), $index);
    }

    public function addRow(array $row): void
    {
        parent::addRow($this->unpackRow($row));
    }

    public function getRow(int $row): RowAccessor
    {
        return new RowAccessor($this, $row);
    }

    public function get(int $row, int|string $column): mixed
    {
        return parent::get($row, $this->keyToIndex($column));
    }

    public function set(int $row, int|string $column, mixed $value = null): void
    {
        parent::set($row, $this->keyToIndex($column), $value);
    }
}
