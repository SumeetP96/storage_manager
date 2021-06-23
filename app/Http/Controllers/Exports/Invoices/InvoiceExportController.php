<?php

namespace App\Http\Controllers\Exports\Invoices;

use PDF;
use App\StockTransferProduct;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\TransferType;

class InvoiceExportController extends Controller
{
    public function invoice($month)
    {
        $transferType = [
            'inter_godown' => 1,
            'purchase' => 2,
            'sales' => 3
        ];
        $transfers = $this->getLotData($month);

        $pdf = PDF::loadView('exports.invoices.invoice', compact('transferType', 'transfers'));
        return $pdf->stream();
        // return $pdf->download('storage_invoice.pdf');
    }

    public function getLotData($month)
    {
        $lotNumbers = StockTransferProduct::distinct()->get(['lot_number']);

        foreach ($lotNumbers as $lot) {
            $lot->transfers = DB::table('stock_transfer_products as stp')
                ->leftJoin('stock_transfers as st', 'stp.stock_transfer_id', '=', 'st.id')
                ->leftJoin('products as pr', 'pr.id', '=', 'stp.product_id')
                ->where('stp.lot_number', $lot->lot_number)
                ->selectRaw('
                    st.date as date, st.transfer_type_id as transferType,
                    st.order_no,
                    pr.name, ROUND(packing / 100, 2) as packing,
                    stp.lot_number,
                    ROUND(stp.rent / 100, 1) as rent,
                    ROUND(stp.loading / 100, 1) as loading,
                    ROUND(stp.unloading / 100, 1) as unloading,
                    ROUND(stp.quantity / 100, 2) as quantity
                ')
                ->orderBy('st.date')
                ->get();
        }

        return $lotNumbers;
    }
}
