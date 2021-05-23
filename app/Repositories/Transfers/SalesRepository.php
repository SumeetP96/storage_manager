<?php

namespace App\Repositories\Transfers;

use App\StockTransfer;
use Illuminate\Http\Request;
use App\GodownProductsStock;
use App\StockTransferProduct;
use Illuminate\Support\Facades\DB;

class SalesRepository
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
            ->where('st.transfer_type_id', StockTransfer::SALES)
            ->leftJoin('agents as ag', 'st.agent_id', '=', 'ag.id')
            ->leftJoin('godowns as fg', 'st.from_godown_id', '=', 'fg.id')
            ->leftJoin('godowns as tg', 'st.to_godown_id', '=', 'tg.id');

        if (!is_null($fromDate) && !is_null($toDate)) {
            $query->whereDate('st.date', '<=', $toDate)->whereDate('st.date', '>=', $fromDate);
        }

        $results = $query->where(function ($query) use ($search) {
            $query->where('fg.name', 'like', '%' . $search . '%')
                ->orWhere('ag.name', 'like', '%' . $search . '%')
                ->orWhere('st.invoice_no', 'like', '%' . $search . '%')
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
                tg.name as toName,
                ag.name as agent
            ');

        $total = $results->count();
        $records = $results->skip($skip)->limit($limit)->orderBy($sortBy, $flow)->get();

        return ['records' => $records, 'total' => $total];
    }

    public function fetchOne($id)
    {
        return DB::table('stock_transfers as st')->where('st.id', $id)
            ->leftJoin('godowns as fg', 'st.from_godown_id', '=', 'fg.id')
            ->leftJoin('godowns as tg', 'st.to_godown_id', '=', 'tg.id')
            ->leftJoin('agents as ag', 'st.agent_id', '=', 'ag.id')
            ->selectRaw('
                st.*,
                DATE_FORMAT(st.date, "%d-%m-%Y") as dateRaw,
                fg.name as fromName,
                tg.name as toName,
                ag.name as agentName
            ')
            ->first();
    }

    public function fetchShowTransferProducts($salesId)
    {
        return DB::table('stock_transfer_products as stp')
            ->where('stp.stock_transfer_id', $salesId)
            ->leftJoin('products as pr', 'pr.id', '=', 'stp.product_id')
            ->selectRaw('
                stp.id,
                stp.quantity,
                stp.quantity div 100 as quantityRaw,
                pr.compound_unit as compoundUnit,
                stp.compound_quantity as compoundQuantity,
                stp.compound_quantity div 100 as compoundQuantityRaw,
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
    public function create(Request $request, $salesService)
    {
        $id = StockTransfer::create([
            'transfer_type_id'  => $request->transfer_type,
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
        ])->id;

        foreach($request->products as $product) {
            StockTransferProduct::create([
                'stock_transfer_id' => $id,
                'product_id'        => $product['id'],
                'quantity'          => $product['quantity'],
                'compound_quantity' => !empty($product['compound_quantity']) ? $product['compound_quantity'] : NULL
            ]);

            if ($existingGPS = $salesService->checkExistingGPS($request, $product['id'])) {
                $this->updateGPS($existingGPS, $product['quantity']);
            } else {
                $this->createGPS($request, $product);
            }
        }
    }

    /**
     * Update Purchase Entry
     */
    public function update(Request $request, $id, $salesService)
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
                'quantity'          => $product['quantity'],
                'compound_quantity' => !empty($product['compound_quantity']) ? $product['compound_quantity'] : NULL
            ]);

            if ($existingGPS = $salesService->checkExistingGPS($request, $product['id'])) {
                $this->updateGPS($existingGPS, $product['quantity']);
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
    }

    /**
     * Undo given changes
     */
    public function undoPreviousGPSChanges($id)
    {
        $stockTransfer = StockTransfer::find($id);
        $products = StockTransferProduct::where('stock_transfer_id', $id)->get();

        foreach($products as $product) {
            $oldGodownStock = GodownProductsStock::where('godown_id', $stockTransfer->from_godown_id)
                ->where('product_id', $product->product_id)
                ->first();

            $oldGodownStock->current_stock += $product->quantity;
            $oldGodownStock->save();
        }
    }

    public function createGPS(Request $request, $product)
    {
        GodownProductsStock::create([
            'product_id'    => $product['id'],
            'godown_id'     => $request->from_godown_id,
            'current_stock' => -$product['quantity']
        ]);
    }

    public function updateGPS(GodownProductsStock $godownStock, $productQuantity)
    {
        $godownStock->current_stock -= $productQuantity;
        $godownStock->save();
    }
}
