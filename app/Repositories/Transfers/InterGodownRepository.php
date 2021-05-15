<?php

namespace App\Repositories\Transfers;

use App\StockTransfer;
use App\GodownProductsStock;
use App\StockTransferProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InterGodownRepository
{
    public function fetchAll(Request $request)
    {
        $skip = $request->get('skip');
        $flow = $request->get('flow');
        $limit = $request->get('limit');
        $search = $request->get('query');
        $sortBy = $request->get('sortBy');

        // Filters
        $toDate = $request->get('to');
        $fromDate = $request->get('from');

        $query = DB::table('stock_transfers as st')
            ->where('st.transfer_type_id', StockTransfer::INTER_GODOWN)
            ->leftJoin('godowns as fg', 'st.from_godown_id', '=', 'fg.id')
            ->leftJoin('godowns as tg', 'st.to_godown_id', '=', 'tg.id');

        if (!is_null($fromDate) && !is_null($toDate)) {
            $query->whereDate('st.date', '<=', $toDate)->whereDate('st.date', '>=', $fromDate);
        }

        $records = $query->where(function ($query) use ($search) {
            $query->where('fg.name', 'like', '%' . $search . '%')
                ->orWhere('st.transfer_no', 'like', '%' . $search . '%')
                ->orWhere('tg.name', 'like', '%' . $search . '%');
            })
            ->selectRaw('
                st.id,
                st.date,
                st.remarks,
                st.invoice_no as invoiceNo,
                st.updated_at,
                st.created_at,
                fg.name as fromName,
                tg.name as toName
            ')
            ->skip($skip)
            ->limit($limit)
            ->orderBy($sortBy, $flow);

        return ['records' => $records->get(), 'total' => $records->count()];
    }

    public function fetchOne($id)
    {
        return DB::table('stock_transfers as st')->where('st.id', $id)
            ->leftJoin('godowns as fg', 'st.from_godown_id', '=', 'fg.id')
            ->leftJoin('godowns as tg', 'st.to_godown_id', '=', 'tg.id')
            ->selectRaw('
                st.*,
                DATE_FORMAT(st.date, "%d-%m-%Y") as dateRaw,
                fg.name as fromName,
                tg.name as toName
            ')
            ->first();
    }

    public function fetchShowTransferProducts($transferId)
    {
        return DB::table('stock_transfer_products as stp')
            ->where('stp.stock_transfer_id', $transferId)
            ->leftJoin('products as pr', 'pr.id', '=', 'stp.product_id')
            ->selectRaw('
                stp.id,
                stp.quantity,
                stp.quantity div 100 as quantityRaw,
                pr.id as productId,
                pr.name as name,
                pr.unit as unit,
                pr.lot_number as lotNumber
            ')
            ->get();
    }

    /**
     * Create Purchase Entry
     */
    public function create(Request $request, $interGodownService)
    {
        $transferNo = StockTransfer::max('transfer_no') + 1;

        $id = StockTransfer::create([
            'transfer_no'       => $transferNo,
            'transfer_type_id'  => $request->transfer_type,
            'date'              => $request->date,
            'from_godown_id'    => $request->from_godown_id,
            'to_godown_id'      => $request->to_godown_id,
            'order_no'          => strtoupper($request->order_no),
            'invoice_no'        => 'TRF/' . $transferNo,
            'eway_bill_no'      => strtoupper($request->eway_bill_no),
            'delivery_slip_no'  => strtoupper($request->delivery_slip_no),
            'transport_details' => strtoupper($request->transport_details),
            'agent_id'          => $request->agent_id,
            'remarks'           => $request->remarks
        ])->id;

        foreach($request->products as $product) {
            StockTransferProduct::create([
                'stock_transfer_id' => $id,
                'product_id'        => $product['id'],
                'quantity'          => $product['quantity']
            ]);

            if ($existingGPS = $interGodownService->checkExistingGPS($request, $product['id'])) {
                $this->updateGPS($existingGPS, $request, $product);
            } else {
                $this->createGPS($request, $product);
            }
        }
    }

    /**
     * Update Purchase Entry
     */
    public function update(Request $request, $id, $interGodownService)
    {
        $this->undoPreviousGPSChanges($id);

        StockTransfer::find($id)->update([
            'date'              => $request->date,
            'from_godown_id'    => $request->from_godown_id,
            'to_godown_id'      => $request->to_godown_id,
            'order_no'          => strtoupper($request->order_no),
            'invoice_no'        => strtoupper($request->invoice_no),
            'eway_bill_no'      => strtoupper($request->eway_bill_no),
            'delivery_slip_no'  => strtoupper($request->delivery_slip_no),
            'transport_details' => strtoupper($request->transport_details),
            'agent_id'          => $request->agent_id,
            'remarks'           => $request->remarks
        ]);

        StockTransferProduct::where('stock_transfer_id', $id)->delete();

        foreach($request->products as $product) {
            StockTransferProduct::create([
                'stock_transfer_id' => $id,
                'product_id'        => $product['id'],
                'quantity'          => $product['quantity']
            ]);

            if ($existingGPS = $interGodownService->checkExistingGPS($request, $product['id'])) {
                $this->updateGPS($existingGPS, $request, $product);
            } else {
                $this->createGPS($request, $product);
            }
        }
    }

    /**
     * Delete record
     */
    public function destroy($id)
    {
        $this->undoPreviousGPSChanges($id);

        StockTransfer::find($id)->delete();

        $this->cleanNoStock();
    }

    /**
     * Undo given changes
     */
    public function undoPreviousGPSChanges($id)
    {
        $stockTransfer = StockTransfer::find($id);
        $products = StockTransferProduct::where('stock_transfer_id', $id)->get();

        foreach ($products as $product) {
            $fromGodownStock = GodownProductsStock::where('godown_id', $stockTransfer->from_godown_id)
                ->where('product_id', $product->product_id)
                ->first();

            $toGodownStock = GodownProductsStock::where('godown_id', $stockTransfer->to_godown_id)
                ->where('product_id', $product->product_id)
                ->first();

            $fromGodownStock->current_stock += $product->quantity;
            $fromGodownStock->save();

            $toGodownStock->current_stock -= $product->quantity;
            $toGodownStock->save();
        }
    }

    public function createGPS(Request $request, $product)
    {
        GodownProductsStock::create([
            'product_id'    => $product['id'],
            'godown_id'     => $request->to_godown_id,
            'current_stock' => $product['quantity']
        ]);

        $from = GodownProductsStock::where('godown_id', $request->from_godown_id)
            ->where('product_id', $product['id'])
            ->first();
        $from->current_stock = $from->current_stock - $product['quantity'];
        $from->save();

        $this->cleanNoStock();
    }

    public function updateGPS(GodownProductsStock $godownStock, Request $request, $product)
    {
        $godownStock->current_stock += $product['quantity'];
        $godownStock->save();

        $from = GodownProductsStock::where('godown_id', $request->from_godown_id)
            ->where('product_id', $product['id'])
            ->first();
        $from->current_stock -= $product['quantity'];
        $from->save();

        $this->cleanNoStock();
    }

    public function cleanNoStock()
    {
        GodownProductsStock::where('current_stock', 0)->delete();
    }
}
