<?php

namespace App\Http\Controllers\Exports\Invoices;

use App\Http\Controllers\Controller;

class InvoiceExportController extends Controller
{
    public function invoice($month)
    {
        // $pdf = PDF::loadView('exports.invoices.invoice', compact());
        $pdf->stream();
        // return $pdf->download('storage_invoice.pdf');
    }
}
