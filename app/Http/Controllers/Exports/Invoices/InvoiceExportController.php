<?php

namespace App\Http\Controllers\Exports\Invoices;

use PDF;
use Carbon\Carbon;
use App\StockTransferProduct;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class InvoiceExportController extends Controller
{
    public function invoicePdf($month, $godownId)
    {
        $transferType = [
            'inter_godown' => 1,
            'purchase' => 2,
            'sales' => 3
        ];

        $monthName = (Carbon::createFromFormat('m', $month))->format('F');

        $data = $this->getLotData($month, $godownId);
        $transfers = $data['transfers'];
        $totals = $data['totals'];

        $pdf = PDF::loadView('exports.invoices.pdf.invoice', compact('transferType', 'transfers', 'totals', 'month'));
        // return $pdf->stream();
        return $pdf->download(strtolower($monthName) . '_storage_invoice.pdf');
    }

    public function invoicePrint($month, $godownId)
    {
        $transferType = [
            'inter_godown' => 1,
            'purchase' => 2,
            'sales' => 3
        ];

        $data = $this->getLotData($month, $godownId);
        $transfers = $data['transfers'];
        $totals = $data['totals'];

        return view('exports.invoices.print.invoice', compact('transferType', 'transfers', 'totals', 'month'));
    }

    public function getLotData($month, $godownId)
    {
        $cDate = Carbon::createFromFormat('m', $month);
        $lastDate = $cDate->endOfMonth()->toDateString(); // Y-m-d
        $lastMonthDate = $this->getLastMonthDate($month);  // Y-m-d

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

        $index = 1;
        $transfers = [];
        $totals = [
            'quantity'  => 0,
            'loading'   => 0,
            'unloading' => 0,
            'total'     => 0
        ];

        foreach ($lotNumbers as $lot) {

            $transfers[$lot->lot_number] = [];

            $closingStock = 0;
            foreach ($lot->transfers as $transfer) {
                if ($transfer->transferType == 2) $closingStock += $transfer->quantity;
            }

            // If no sales or transfers only purchase
            if (count($lot->transfers) == 1 && $lot->transfers[0]->transferType == 2) {
                $trf = $lot->transfers[0];

                $closingStock = $trf->quantity;
                $monthRangeStart = strtotime(date('Y') . '/' . (strlen($month) == 1 ? '0' . $month : $month) . '/' . '01');
                $monthRangeEnd = strtotime(date('Y-m-d', strtotime($lastDate)));

                $monthCount = 0;
                $inwardDate = $this->getInwardDate($lot); // d/m/Y
                $outwardDate = $trf->date; // Y-m-d

                // First Invoice
                $firstInvoice = false;
                $inwardTime = strtotime((Carbon::createFromFormat('d/m/Y', $inwardDate))->toDateString());
                $thisTime = strtotime($lastDate);
                $lastMonthTime = strtotime($lastMonthDate);

                if ($inwardTime >= $lastMonthTime && $inwardTime <= $thisTime) {
                    // First invoice
                    $firstInvoice = true;
                    $monthCount = 1;
                } else {
                    // Second Invoice
                    $diffFromInwardDays = (Carbon::createFromFormat('Y-m-d', $outwardDate))
                        ->diffInDays(Carbon::createFromFormat('d/m/Y', $inwardDate));
                    if ($diffFromInwardDays <= 30) {
                        $monthCount = 0;
                    } else {
                        while ($diffFromInwardDays > 30) {
                            $diffFromInwardDays -= 30;
                        }
                        if ($diffFromInwardDays < 15) $monthCount = 0.5;
                        else $monthCount = 1;
                    }
                }

                $outwardDate = $lastDate;
                if ($inwardTime >= $lastMonthTime && $inwardTime <= $thisTime) {
                    // First invoice
                    $firstInvoice = true;
                    $monthCount = 1;
                } else {
                    // Second Invoice
                    $diffFromInwardDays = (Carbon::createFromFormat('Y-m-d', $outwardDate))
                        ->diffInDays(Carbon::createFromFormat('d/m/Y', $inwardDate));
                    if ($diffFromInwardDays <= 30) {
                        $monthCount = 0;
                    } else {
                        while ($diffFromInwardDays > 30) {
                            $diffFromInwardDays -= 30;
                        }
                        if ($diffFromInwardDays < 15) $monthCount = 0.5;
                        else $monthCount = 1;
                    }
                }

                array_push($transfers[$lot->lot_number], [
                    'index'         => $index++,
                    'name'          => $trf->name,
                    'quantity'      => number_format($closingStock, 2),
                    'inward_date'   => $this->getInwardDate($lot),
                    'outward_date'  => date('d/m/Y', strtotime($lastDate)),
                    'outward_no'    => 'Balance',
                    'month'         => $monthCount,
                    'packing'       => $trf->packing,
                    'rent'          => $trf->rent,
                    'amount'        => number_format($monthCount * $closingStock * $trf->rent, 2)
                ]);

                if ($firstInvoice) $totals['unloading'] += ($closingStock * $trf->packing / 100) * $trf->unloading;
                $totals['quantity'] += $closingStock;
                $totals['total'] += $monthCount * $closingStock * $trf->rent;

            } else {
                foreach ($lot->transfers as $i => $trf) { // Each transfer under lot number

                    if ($trf->transferType != '2') { // If not purchase

                        $closingStock -= $trf->quantity;
                        $monthRangeStart = strtotime(date('Y') . '/' . (strlen($month) == 1 ? '0' . $month : $month) . '/' . '01');
                        $monthRangeEnd = strtotime(date('Y-m-d', strtotime($lastDate)));

                        $monthCount = 0;
                        $inwardDate = $this->getInwardDate($lot); // d/m/Y
                        $outwardDate = $trf->date; // Y-m-d

                        // First Invoice
                        $firstInvoice = false;
                        $inwardTime = strtotime((Carbon::createFromFormat('d/m/Y', $inwardDate))->toDateString());
                        $thisTime = strtotime($lastDate);
                        $lastMonthTime = strtotime($lastMonthDate);

                        if ($inwardTime >= $lastMonthTime && $inwardTime <= $thisTime) {
                            // First invoice
                            $firstInvoice = true;
                            $monthCount = 1;
                        } else {
                            // Second Invoice
                            $diffFromInwardDays = (Carbon::createFromFormat('Y-m-d', $outwardDate))
                                ->diffInDays(Carbon::createFromFormat('d/m/Y', $inwardDate));
                            if ($diffFromInwardDays <= 30) {
                                $monthCount = 0;
                            } else {
                                while ($diffFromInwardDays > 30) {
                                    $diffFromInwardDays -= 30;
                                }
                                if ($diffFromInwardDays < 15) $monthCount = 0.5;
                                else $monthCount = 1;
                            }
                        }

                        if (strtotime($trf->date) >= $monthRangeStart && strtotime($trf->date) <= $monthRangeEnd) {

                            if ($firstInvoice) $totals['unloading'] += ($trf->quantity * $trf->packing / 100) * $trf->unloading;
                            $totals['quantity'] += $trf->quantity;
                            $totals['loading'] += ($trf->quantity * $trf->packing / 100) * $trf->loading;
                            $totals['total'] += $monthCount * $trf->quantity * $trf->rent;

                            // Transfer stock
                            array_push($transfers[$lot->lot_number], [
                                'index'         => $index++,
                                'name'          => $trf->name,
                                'quantity'      => $trf->quantity,
                                'inward_date'   => $this->getInwardDate($lot),
                                'outward_date'  => date('d/m/Y', strtotime($outwardDate)),
                                'outward_no'    => $trf->order_no ? $trf->order_no : '-',
                                'month'         => $monthCount,
                                'packing'       => $trf->packing,
                                'rent'          => $trf->rent,
                                'amount'        => number_format($monthCount * $trf->quantity * $trf->rent, 2)
                            ]);
                        }

                        // Balance stock
                        if ($i == count($lot->transfers) - 1 && $closingStock != 0) {
                            $outwardDate = $lastDate;
                            if ($inwardTime >= $lastMonthTime && $inwardTime <= $thisTime) {
                                // First invoice
                                $firstInvoice = true;
                                $monthCount = 1;
                            } else {
                                // Second Invoice
                                $diffFromInwardDays = (Carbon::createFromFormat('Y-m-d', $outwardDate))
                                    ->diffInDays(Carbon::createFromFormat('d/m/Y', $inwardDate));
                                if ($diffFromInwardDays <= 30) {
                                    $monthCount = 0;
                                } else {
                                    while ($diffFromInwardDays > 30) {
                                        $diffFromInwardDays -= 30;
                                    }
                                    if ($diffFromInwardDays < 15) $monthCount = 0.5;
                                    else $monthCount = 1;
                                }
                            }

                            array_push($transfers[$lot->lot_number], [
                                'index'         => $index++,
                                'name'          => $trf->name,
                                'quantity'      => number_format($closingStock, 2),
                                'inward_date'   => $this->getInwardDate($lot),
                                'outward_date'  => date('d/m/Y', strtotime($lastDate)),
                                'outward_no'    => 'Balance',
                                'month'         => $monthCount,
                                'packing'       => $trf->packing,
                                'rent'          => $trf->rent,
                                'amount'        => number_format($monthCount * $closingStock * $trf->rent, 2)
                            ]);

                            if ($firstInvoice) $totals['unloading'] += ($closingStock * $trf->packing / 100) * $trf->unloading;
                            $totals['quantity'] += $closingStock;
                            $totals['total'] += $monthCount * $closingStock * $trf->rent;
                        }
                    }
                }
            }

        }

        return ['transfers' => $transfers, 'totals' => $totals];
    }

    public function getInwardDate($lot)
    {
        foreach ($lot->transfers as $transfer) {
            if ($transfer->transferType == 2) {
                return date('d/m/Y', strtotime($transfer->date));
            }
        }
    }

    public function getLastMonthDate($month)
    {
        if ($month == 1) {
            $date = Carbon::createFromFormat('m/Y', '12' . '/' . ((int) date('Y') - 1));
            return $date->endOfMonth()->toDateString();
        } else {
            $date = Carbon::createFromFormat('m', ((int) $month - 1));
            return $date->endOfMonth()->toDateString();
        }
    }
}
