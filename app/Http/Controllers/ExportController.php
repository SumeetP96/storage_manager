<?php

namespace App\Http\Controllers;

use PDF;
use Illuminate\Http\Request;
use App\Exports\ProductExport;
use Illuminate\Support\Facades\DB;

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

    public function productPdf(Request $request)
    {
        $flow = $request->get('flow') ?? 'asc';
        $search = $request->get('query') ?? '';
        $sortBy = $request->get('sortBy') ?? 'created_at';

        $records = DB::table('products')
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('alias', 'like', '%' . $search . '%')
                    ->orWhere('remarks', 'like', '%' . $search . '%')
                    ->orWhere('lot_number', 'like', '%' . $search . '%');
            })
            ->orderBy($sortBy, $flow)
            ->get();

        $pdf = PDF::loadView('exports.pdf.products', compact('records'));
        return $pdf->download('products.pdf');
    }
}
