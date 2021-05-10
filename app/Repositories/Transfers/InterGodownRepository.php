<?php

namespace App\Repositories\Transfers;

use App\StockTransfer;
use App\GodownProductsStock;
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

        $toDate = $request->get('to');
        $fromDate = $request->get('from');

        // If no date range selected
        if (!is_null($fromDate) && !is_null($toDate)) {
            $records = DB::table('stock_transfers as st')
            ->where('st.transfer_type_id', StockTransfer::INTER_GODOWN)
            ->leftJoin('godowns as fg', 'st.from_godown_id', '=', 'fg.id')
            ->leftJoin('godowns as tg', 'st.to_godown_id', '=', 'tg.id')
            ->leftJoin('products as pr', 'st.product_id', '=', 'pr.id')
            ->whereDate('st.date', '<=', $toDate)
            ->whereDate('st.date', '>=', $fromDate)
            ->where(function ($query) use ($search) {
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
            ->limit($limit)->skip($skip)
            ->orderBy($sortBy, $flow);
        } else {
            $records = DB::table('stock_transfers as st')
            ->where('st.transfer_type_id', StockTransfer::INTER_GODOWN)
            ->leftJoin('godowns as fg', 'st.from_godown_id', '=', 'fg.id')
            ->leftJoin('godowns as tg', 'st.to_godown_id', '=', 'tg.id')
            ->leftJoin('products as pr', 'st.product_id', '=', 'pr.id')
            ->where(function ($query) use ($search) {
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
            ->limit($limit)->skip($skip)
            ->orderBy($sortBy, $flow);
        }

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

        $fromGodownStock = GodownProductsStock::where('godown_id', $stockTransfer->from_godown_id)
            ->where('product_id', $stockTransfer->product_id)
            ->first();

        $toGodownStock = GodownProductsStock::where('godown_id', $stockTransfer->to_godown_id)
            ->where('product_id', $stockTransfer->product_id)
            ->first();

        $fromGodownStock->current_stock += $stockTransfer->quantity;
        $fromGodownStock->save();

        $toGodownStock->current_stock -= $stockTransfer->quantity;
        $toGodownStock->save();
    }

    public function createGPS(Request $request)
    {
        GodownProductsStock::create([
            'product_id'    => $request->product_id,
            'godown_id'     => $request->to_godown_id,
            'current_stock' => $request->quantity
        ]);

        $from = GodownProductsStock::where('godown_id', $request->from_godown_id)
            ->where('product_id', $request->product_id)
            ->first();
        $from->current_stock = $from->current_stock - $request->quantity;
        $from->save();

        $this->cleanNoStock();
    }

    public function updateGPS(GodownProductsStock $godownStock, Request $request)
    {
        $godownStock->current_stock += $request->quantity;
        $godownStock->save();

        $from = GodownProductsStock::where('godown_id', $request->from_godown_id)
            ->where('product_id', $request->product_id)
            ->first();
        $from->current_stock -= $request->quantity;
        $from->save();

        $this->cleanNoStock();
    }

    public function cleanNoStock()
    {
        GodownProductsStock::where('current_stock', 0)->delete();
    }
}
