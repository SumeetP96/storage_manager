<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\ProductExport;

class ExportController extends Controller
{
    public function productExcel(Request $request)
    {
        $query = [
            'id'    => NULL
        ];

        return (new ProductExport($query))->download('products.xlsx');
    }
}
