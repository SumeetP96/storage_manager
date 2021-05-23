<?php

namespace App\Exports;

use App\Product;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;

class ProductExport implements FromQuery
{
    use Exportable;

    public function __construct(array $query = NULL)
    {
        $this->query = $query;
    }

    public function query()
    {
        return Product::query();
    }
}
