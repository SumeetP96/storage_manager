<?php

namespace App\Repositories\Transfers;

use App\StockTransfer;
use Illuminate\Http\Request;
use App\GodownProductsStock;
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
            ->leftJoin('godowns as fg', 'st.from_godown_id', '=', 'fg.id')
            ->leftJoin('godowns as tg', 'st.to_godown_id', '=', 'tg.id')
            ->leftJoin('products as pr', 'st.product_id', '=', 'pr.id');

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
                ->orWhere('st.quantity', 'like', '%' . $search . '%')
                ->orWhere('tg.name', 'like', '%' . $search . '%')
                ->orWhere('pr.name', 'like', '%' . $search . '%')
                ->orWhere('pr.unit', 'like', '%' . $search . '%')
                ->orWhere('pr.lot_number', 'like', '%' . $search . '%');
            })
            ->selectRaw('
                st.id,
                st.date,
                st.quantity,
                st.updated_at,
                st.created_at,
                st.remarks,
                fg.name as fromName,
                tg.name as toName,
                pr.name as productName,
                pr.unit as productUnit,
                pr.lot_number as productLotNumber
            ')
            ->skip($skip)
            ->limit($limit)
            ->orderBy($sortBy, $flow);

        return ['records' => $records->get(), 'total' => $records->count()];
    }

    public function fetchOne($id)
    {
        $record = DB::table('stock_transfers as st')->where('st.id', $id)
            ->leftJoin('godowns as fg', 'st.from_godown_id', '=', 'fg.id')
            ->leftJoin('godowns as tg', 'st.to_godown_id', '=', 'tg.id')
            ->leftJoin('products as pr', 'st.product_id', '=', 'pr.id')
            ->leftJoin('agents as ag', 'st.agent_id', '=', 'ag.id')
            ->selectRaw('
                st.*,
                DATE_FORMAT(st.date, "%d-%m-%Y") as dateRaw,
                st.quantity div 100 as quantityRaw,
                fg.name as fromName,
                tg.name as toName,
                pr.name as productName,
                pr.unit as productUnit,
                pr.lot_number as productLotNumber,
                ag.name as agentName
            ')->first();

        return $record;
    }

    /**
     * Create Purchase Entry
     */
    public function create(Request $request)
    {
        StockTransfer::create([
            'transfer_type_id'  => $request->transfer_type,
            'date'              => $request->date,
            'from_godown_id'    => $request->from_godown_id,
            'product_id'        => $request->product_id,
            'quantity'          => $request->quantity,
            'to_godown_id'      => $request->to_godown_id,
            'order_no'          => strtoupper($request->order_no),
            'invoice_no'        => strtoupper($request->invoice_no),
            'eway_bill_no'      => strtoupper($request->eway_bill_no),
            'delivery_slip_no'  => strtoupper($request->delivery_slip_no),
            'transport_details' => strtoupper($request->transport_details),
            'agent_id'          => $request->agent_id,
            'remarks'           => $request->remarks
        ]);
    }

    /**
     * Update Purchase Entry
     */
    public function update(Request $request, $id)
    {
        $this->undoPreviousGPSChanges($id);

        StockTransfer::find($id)->update([
            'date'              => $request->date,
            'from_godown_id'    => $request->from_godown_id,
            'product_id'        => $request->product_id,
            'quantity'          => $request->quantity,
            'to_godown_id'      => $request->to_godown_id,
            'order_no'          => strtoupper($request->order_no),
            'invoice_no'        => strtoupper($request->invoice_no),
            'eway_bill_no'      => strtoupper($request->eway_bill_no),
            'delivery_slip_no'  => strtoupper($request->delivery_slip_no),
            'transport_details' => strtoupper($request->transport_details),
            'agent_id'          => $request->agent_id,
            'remarks'           => $request->remarks
        ]);
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

        $oldGodownStock = GodownProductsStock::where('godown_id', $stockTransfer->to_godown_id)
            ->where('product_id', $stockTransfer->product_id)
            ->first();

        $oldCurrentStock = $oldGodownStock->current_stock;
        $oldGodownStock->current_stock = $oldCurrentStock - $stockTransfer->quantity;
        $oldGodownStock->save();
    }

    public function createGPS(Request $request)
    {
        GodownProductsStock::create([
            'product_id'    => $request->product_id,
            'godown_id'     => $request->to_godown_id,
            'current_stock' => $request->quantity
        ]);

        $this->cleanNoStock();
    }

    public function updateGPS(GodownProductsStock $godownStock, Request $request)
    {
        $currentStock = $godownStock->current_stock;
        $godownStock->current_stock = $currentStock + $request->quantity;
        $godownStock->save();

        $this->cleanNoStock();
    }

    public function cleanNoStock()
    {
        GodownProductsStock::where('current_stock', 0)->delete();
    }
}
