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

        $data = $this->getLotData($month);
        $transfers = $data['transfers'];
        $totals = $data['totals'];

        dd($transfers, $totals);
        $pdf = PDF::loadView('exports.invoices.invoice', compact('transferType', 'transfers', 'totals', 'month'));
        return $pdf->stream();
        // return $pdf->download('storage_invoice.pdf');
    }

    public function getLotData($month)
    {
        $lastDay = $month == 2 ? '28' : '30';
        $month = strlen($month) == 1 ? '0' . $month : $month;
        $date = date('Y') . '-' . $month . '-' . $lastDay;

        $lotNumbers = StockTransferProduct::distinct()->get(['lot_number']);

        foreach ($lotNumbers as $lot) {
            $lot->transfers = DB::table('stock_transfer_products as stp')
                ->leftJoin('stock_transfers as st', 'stp.stock_transfer_id', '=', 'st.id')
                ->leftJoin('products as pr', 'pr.id', '=', 'stp.product_id')
                ->where('stp.lot_number', $lot->lot_number)
                ->whereDate('st.date', '<=', $date)
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


        $index = 1;
        $month = 1.0;
        $total = 0;
        $quantity = 0;
        $loading = 0;
        $unloading = 0;

        $transfers = [];
        $totals = [];

        foreach ($lotNumbers as $lot) {
            $transfers[$lot->lot_number] = [];

            $closingStock = 0;
            foreach ($lot->transfers as $transfer) {
                if ($transfer->transferType == 2) $closingStock += $transfer->quantity;
            }

            foreach ($lot->transfers as $i => $trf) {

                $closingStock -= $trf->quantity;
                $totals['quantity'] += $trf->quantity;
                $totals['loading'] += ($trf->quantity * $trf->packing / 100) * $trf->loading;
                $totals['unloading'] += ($trf->quantity * $trf->packing / 100) * $trf->unloading;
                $totals['total'] += $month * $trf->quantity * $trf->rent;

                if ($trf->transferType != 2) {
                    array_push($transfers[$lot->lot_number], [
                        'index'         => $index++,
                        'name'          => $trf->name,
                        'quantity'      => $trf->quantity,
                        'inward_date'   => $this->getInwardDate($lot),
                        'outward_date'  => date('d-m-Y', strtotime($trf->date)),
                        'outward_no'    => $trf->order_no,
                        'month'         => $month,
                        'packing'       => $trf->packing,
                        'rent'          => $trf->rent,
                        'amount'        => number_format($month * $trf->quantity * $trf->rent, 2)
                    ]);
                }

                if ($i == count($transfer->transfers) - 1 && $closingStock > 0) {
                    array_push($transfers[$lot->lot_number], [
                        'index'         => $index++,
                        'name'          => $trf->name,
                        'quantity'      => number_format($closingStock, 2),
                        'inward_date'   => $this->getInwardDate($lot),
                        'outward_date'  => date('d-m-Y', strtotime($trf->date)),
                        'outward_no'    => 'BALANCE',
                        'month'         => '1.0',
                        'packing'       => $trf->packing,
                        'rent'          => $trf->rent,
                        'amount'        => number_format($month * $closingStock * $trf->rent, 2)
                    ]);

                    $totals['quantity'] += $closingStock;
                    $totals['unloading'] += ($closingStock * $trf->packing / 100) * $trf->unloading;
                    $totals['total'] += $month * $closingStock * $trf->rent;
                }
            }
        }

        return ['transfers' => $transfers, 'totals' => $totals];
    }

    public function getInwardDate($lot)
    {
        foreach ($lot->transfers as $transfer) {
            if ($transfer->transferType == 2) {
                return date('d-m-Y', strtotime($transfer->date));
            }
        }
    }
}
