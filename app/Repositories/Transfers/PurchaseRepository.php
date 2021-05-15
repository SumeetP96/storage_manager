<?php

namespace App\Repositories\Transfers;

use App\StockTransfer;
use Illuminate\Http\Request;
use App\GodownProductsStock;
use App\StockTransferProduct;
use Illuminate\Support\Facades\DB;

class PurchaseRepository
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

        // $fromOnlyIds = $request->get('fromOnlyIds');
        // $fromExceptIds = $request->get('fromExceptIds');

        // $toOnlyIds = $request->get('toOnlyIds');
        // $toExceptIds = $request->get('toExceptIds');

        // $productOnlyIds = $request->get('productOnlyIds');
        // $productExceptIds = $request->get('productExceptIds');

        // $lotOnly = $request->get('lotOnly');
        // $lotExcept = $request->get('lotExcept');
        // $lotNull = (bool) $request->get('lotNull');

        // $unitOnly = $request->get('unitOnly');
        // $unitExcept = $request->get('unitExcept');

        // $quantityGt = $request->get('quantityGt');
        // $quantityLt = $request->get('quantityLt');

        $query = DB::table('stock_transfers as st')
            ->where('st.transfer_type_id', StockTransfer::PURCHASE)
            ->leftJoin('agents as ag', 'st.agent_id', '=', 'ag.id')
            ->leftJoin('godowns as fg', 'st.from_godown_id', '=', 'fg.id')
            ->leftJoin('godowns as tg', 'st.to_godown_id', '=', 'tg.id');

        if (!is_null($fromDate) && !is_null($toDate)) {
            $query->whereDate('st.date', '<=', $toDate)->whereDate('st.date', '>=', $fromDate);
        }

        // if (!is_null($fromOnlyIds)) $query->whereIn('fg.id', explode(',', $fromOnlyIds));
        // if (!is_null($fromExceptIds)) $query->whereNotIn('fg.id', explode(',', $fromExceptIds));

        // if (!is_null($toOnlyIds)) $query->whereIn('tg.id', explode(',', $toOnlyIds));
        // if (!is_null($toExceptIds)) $query->whereNotIn('tg.id', explode(',', $toExceptIds));

        // if (!is_null($productOnlyIds)) $query->whereIn('pr.id', explode(',', $productOnlyIds));
        // if (!is_null($productExceptIds)) $query->whereNotIn('pr.id', explode(',', $productExceptIds));

        // if ($lotNull) {
        //     $query->where('pr.lot_number', NULL);
        // } else {
        //     if (!is_null($lotOnly)) $query->whereIn('pr.lot_number', explode(',', $lotOnly));
        //     if (!is_null($lotExcept)) $query->whereNotIn('pr.lot_number', explode(',', $lotExcept));
        // }

        // if (!is_null($unitOnly)) $query->whereIn('pr.unit', explode(',', $unitOnly));
        // if (!is_null($unitExcept)) $query->whereNotIn('pr.unit', explode(',', $unitExcept));

        // if (!is_null($quantityGt)) $query->where('st.quantity', '>', $quantityGt * 100);
        // if (!is_null($quantityLt)) $query->where('st.quantity', '<', $quantityLt * 100);

        $records = $query->where(function ($query) use ($search) {
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

    public function fetchShowTransferProducts($purchaseId)
    {
        return DB::table('stock_transfer_products as stp')
            ->where('stp.stock_transfer_id', $purchaseId)
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
    public function create(Request $request, $purchaseService)
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
                'quantity'          => $product['quantity']
            ]);

            if ($existingGPS = $purchaseService->checkExistingGPS($request, $product['id'])) {
                $this->updateGPS($existingGPS, $product['quantity']);
            } else {
                $this->createGPS($request, $product);
            }
        }
    }

    /**
     * Update Purchase Entry
     */
    public function update(Request $request, $id, $purchaseService)
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

            if ($existingGPS = $purchaseService->checkExistingGPS($request, $product['id'])) {
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

        $this->cleanNoStock();
    }

    /**
     * Undo given changes
     */
    public function undoPreviousGPSChanges($id)
    {
        $stockTransfer = StockTransfer::find($id);
        $products = StockTransferProduct::where('stock_transfer_id', $id)->get();

        foreach($products as $product) {
            $oldGodownStock = GodownProductsStock::where('godown_id', $stockTransfer->to_godown_id)
                ->where('product_id', $product->product_id)
                ->first();

            $oldGodownStock->current_stock -= $product->quantity;
            $oldGodownStock->save();
        }
    }

    public function createGPS(Request $request, $product)
    {
        GodownProductsStock::create([
            'product_id'    => $product['id'],
            'godown_id'     => $request->to_godown_id,
            'current_stock' => $product['quantity']
        ]);

        $this->cleanNoStock();
    }

    public function updateGPS(GodownProductsStock $godownStock, $productQuantity)
    {
        $godownStock->current_stock += $productQuantity;
        $godownStock->save();

        $this->cleanNoStock();
    }

    public function cleanNoStock()
    {
        GodownProductsStock::where('current_stock', 0)->delete();
    }
}
