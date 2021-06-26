<?php

namespace App\Http\Controllers\Exports\Invoices;

use PDF;
use Carbon\Carbon;
use App\StockTransferProduct;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class InvoiceExportController extends Controller
{
    public function invoice($month, $godownId)
    {
        $transferType = [
            'inter_godown' => 1,
            'purchase' => 2,
            'sales' => 3
        ];

        $data = $this->getLotData($month, $godownId);
        $transfers = $data['transfers'];
        $totals = $data['totals'];

        $pdf = PDF::loadView('exports.invoices.invoice', compact('transferType', 'transfers', 'totals', 'month'));
        return $pdf->stream();
        // return $pdf->download('storage_invoice.pdf');
    }

    public function getLotData($month, $godownId)
    {
        $cDate = Carbon::createFromFormat('m', $month);
        $month = strlen($month) == 1 ? '0' . $month : $month;
        $lastDate = $cDate->endOfMonth()->toDateString();

        $lotNumbers = StockTransferProduct::distinct()->get(['lot_number']);

        foreach ($lotNumbers as $lot) {
            $lot->transfers = DB::table('stock_transfer_products as stp')
                ->leftJoin('stock_transfers as st', 'stp.stock_transfer_id', '=', 'st.id')
                ->leftJoin('products as pr', 'pr.id', '=', 'stp.product_id')
                ->where('stp.lot_number', $lot->lot_number)
                ->where(function ($query) use ($godownId) {
                    $query->where('from_godown_id', $godownId)
                        ->orWhere('to_godown_id', $godownId);
                })
                ->whereDate('st.date', '<=', $lastDate)
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

        foreach ($lotNumbers as $lot) {

            foreach ($lot->transfers as $trf) { // Each transfer under lot number

                if ($trf->transferType != '2') { // If not purchase

                    $inwardDate = $this->getInwardDate($lot); // d/m/Y
                    $trfDate = $trf->date; // d-m-Y

                    $inwardStart = Carbon::createFromFormat('d/m/Y', $inwardDate);
                    $inwardEnd = Carbon::createFromFormat('d-m-Y', date('d-m-Y', strtotime($trfDate)));
                    $diffFromInward = $inwardStart->diffInDays($inwardEnd);

                    if ($diffFromInward <= 30) { // if first month
                        $monthCount = 1;

                    } else { // check if this month's invoice was generated last month


                    }
                }
            }
        }
























        // $index = 1;
        // $transfers = [];
        // $totals = [
        //     'quantity'  => 0,
        //     'loading'   => 0,
        //     'unloading' => 0,
        //     'total'     => 0
        // ];

        // foreach ($lotNumbers as $lot) {
        //     $transfers[$lot->lot_number] = [];

        //     $closingStock = 0;
        //     foreach ($lot->transfers as $transfer) {
        //         if ($transfer->transferType == 2) $closingStock += $transfer->quantity;
        //     }

        //     foreach ($lot->transfers as $i => $trf) {

        //         if ($trf->transferType != 2) {
        //             $closingStock -= $trf->quantity;

        //             $start = strtotime(date('Y') . '/' . (strlen($month) == 1 ? '0' . $month : $month) . '/' . '01');

        //             if (strtotime($trf->date) >= $start && strtotime($trf->date) <= strtotime($date)) {

        //                 $monthCount = $this->countMonths($month, $lastDay, $trf->date, $this->getInwardDate($lot));

        //                 $totals['quantity'] += $trf->quantity;
        //                 $totals['loading'] += ($trf->quantity * $trf->packing / 100) * $trf->loading;
        //                 $totals['unloading'] += ($trf->quantity * $trf->packing / 100) * $trf->unloading;
        //                 $totals['total'] += $monthCount * $trf->quantity * $trf->rent;

        //                 array_push($transfers[$lot->lot_number], [
        //                     'index'         => $index++,
        //                     'name'          => $trf->name,
        //                     'quantity'      => $trf->quantity,
        //                     'inward_date'   => $this->getInwardDate($lot),
        //                     'outward_date'  => date('d/m/Y', strtotime($trf->date)),
        //                     'outward_no'    => $trf->order_no ? $trf->order_no : '-',
        //                     'month'         => $monthCount,
        //                     'packing'       => $trf->packing,
        //                     'rent'          => $trf->rent,
        //                     'amount'        => number_format($monthCount * $trf->quantity * $trf->rent, 2)
        //                 ]);

        //                 if ($i == count($lot->transfers) - 1 && $closingStock != 0) {
        //                     $monthCount = 1;

        //                     array_push($transfers[$lot->lot_number], [
        //                         'index'         => $index++,
        //                         'name'          => $trf->name,
        //                         'quantity'      => number_format($closingStock, 2),
        //                         'inward_date'   => $this->getInwardDate($lot),
        //                         'outward_date'  => date('d/m/Y', strtotime($trf->date)),
        //                         'outward_no'    => 'BAL',
        //                         'month'         => $monthCount,
        //                         'packing'       => $trf->packing,
        //                         'rent'          => $trf->rent,
        //                         'amount'        => number_format($monthCount * $closingStock * $trf->rent, 2)
        //                     ]);

        //                     $totals['quantity'] += $closingStock;
        //                     $totals['unloading'] += ($closingStock * $trf->packing / 100) * $trf->unloading;
        //                     $totals['total'] += $monthCount * $closingStock * $trf->rent;
        //                 }
        //             }
        //         }

        //     }
        // }

        // return ['transfers' => $transfers, 'totals' => $totals];
    }

    public function getInwardDate($lot)
    {
        foreach ($lot->transfers as $transfer) {
            if ($transfer->transferType == 2) {
                return date('d/m/Y', strtotime($transfer->date));
            }
        }
    }

    // public function countMonths($month, $lastDay, $transferDate, $inwardDate)
    // {
    //     $previousDate = date('Y') . '/' . $month . '/' . $lastDay;

    //     $inward = Carbon::createFromFormat('d/m/Y', $inwardDate);
    //     $start = Carbon::createFromFormat('Y/m/d', $previousDate);
    //     $end = Carbon::createFromFormat('Y/m/d', date('Y/m/d', strtotime($transferDate)));
    //     $diff = $start->diffInDays($end);
    //     $diffInward = $inward->diffInDays($end);

    //     if ($diffInward <= 30) return 1;

    //     if ($diff < 15) return 0.5;
    //     else return 1;
    // }
}
