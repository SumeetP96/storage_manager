<?php

namespace App\Http\Controllers;

use App\Exports\ProductExport;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    public function productExcel(Request $request)
    {
        return (new ProductExport([
            'flow'      => $request->get('flow'),
            'query'     => $request->get('query'),
            'sortBy'    => $request->get('sortBy')
        ]))->download('products.xlsx');
    }
}
