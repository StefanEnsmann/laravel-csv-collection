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
        parent::__construct($collection);
    }
}
