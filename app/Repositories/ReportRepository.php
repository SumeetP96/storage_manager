<?php

namespace App\Repositories;

use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportRepository
{
    /**
     * Fetch product wise total stock
     *
     * @param  mixed $request
     * @return array $records, $total
     */
    public function fetchProductsStock(Request $request)
    {
        $skip = $request->get('skip');
        $flow = $request->get('flow');
        $limit = $request->get('limit');
        $search = $request->get('query');
        $sortBy = $request->get('sortBy');

        $results = DB::table('godown_products_stocks as gps')
            ->leftJoin('products as pr', 'gps.product_id', '=', 'pr.id')
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('unit', 'like', '%' . $search . '%');
            })
            ->selectRaw('
                pr.unit as unit,
                pr.name as name,
                sum(gps.current_stock div pr.packing) as compoundStock,
                pr.compound_unit as compoundUnit,
                sum(gps.current_stock) as stock
            ')
            ->groupBy('name', 'unit', 'compound_unit');

        $total = count($results->get());
        $records = $results->skip($skip)->limit($limit)->orderBy($sortBy, $flow)->get();

        return ['records' => $records, 'total' => $total];
    }


    /**
     * Fetch godown wise product stock
     *
     * @param  mixed $request
     * @return array $record, $total
     */
    public function fetchGodownProductsStock(Request $request)
    {
        $flow = $request->get('flow');
        $skip = $request->get('skip');
        $limit = $request->get('limit');
        $search = $request->get('query');
        $sortBy = $request->get('sortBy');

        $results = DB::table('godown_products_stocks as gps')
            ->leftJoin('godowns as gd', 'gps.godown_id', '=', 'gd.id')
            ->leftJoin('products as pr', 'gps.product_id', '=', 'pr.id')
            ->where('gps.current_stock', '>', 0)
            ->where(function ($query) use ($search) {
                $query->where('pr.name', 'like', '%' . $search . '%')
                    ->orWhere('gd.name', 'like', '%' . $search . '%')
                    ->orWhere('pr.lot_number', 'like', '%' . $search . '%');
            })
            ->selectRaw('
                gps.updated_at,
                gps.created_at,
                gps.current_stock as currentStock,
                gd.name as godownName,
                pr.name as productName,
                pr.unit as productUnit,
                gps.current_stock div pr.packing as compoundStock,
                pr.packing,
                pr.compound_unit as compoundUnit,
                pr.lot_number as productLotNumber
            ');

        $total = $results->count();
        $records = $results->skip($skip)->limit($limit)->orderBy($sortBy, $flow)->get();

        return ['records' => $records, 'total' => $total];
    }

    /**
     * Fetch lot wise total stock
     *
     * @param  mixed $request
     * @return array $records, $total
     */
    public function fetchLotStock(Request $request)
    {
        $skip = $request->get('skip');
        $flow = $request->get('flow');
        $limit = $request->get('limit');
        $search = $request->get('query');
        $sortBy = $request->get('sortBy');

        $results = DB::table('godown_products_stocks as gps')
            ->leftJoin('products as pr', 'gps.product_id', '=', 'pr.id')
            ->where(function ($query) use ($search) {
                $query->where('pr.lot_number', 'like', '%' . $search . '%')
                    ->orWhere('pr.unit', 'like', '%' . $search . '%');
            })
            ->selectRaw('
                pr.unit as unit,
                pr.lot_number as lotNumber,
                pr.compound_unit as compoundUnit,
                sum(gps.current_stock div pr.packing) as compoundStock,
                sum(gps.current_stock) as stock
            ')
            ->groupBy('lot_number', 'unit', 'compound_unit');

        $total = count($results->get());
        $records = $results->skip($skip)->limit($limit)->orderBy($sortBy, $flow)->get();

        return ['records' => $records, 'total' => $total];
    }

    /**
     * Fetch lot wise products stock
     *
     * @param  mixed $request
     * @return array $records, $total
     */
    public function fetchProductsLotStock(Request $request)
    {
        $skip = $request->get('skip');
        $flow = $request->get('flow');
        $limit = $request->get('limit');
        $search = $request->get('query');
        $sortBy = $request->get('sortBy');

        $results = DB::table('godown_products_stocks as gps')
            ->leftJoin('products as pr', 'gps.product_id', '=', 'pr.id')
            ->where('gps.current_stock', '>', 0)
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('lot_number', 'like', '%' . $search . '%')
                    ->orWhere('unit', 'like', '%' . $search . '%');
            })
            ->selectRaw('
                pr.unit as productUnit,
                pr.lot_number as lotNumber,
                pr.name as name,
                gps.current_stock div pr.packing as compoundStock,
                pr.packing,
                pr.compound_unit as compoundUnit,
                sum(gps.current_stock) as currentStock
            ')
            ->groupBy('lot_number', 'unit', 'name', 'compound_unit', 'packing', 'current_stock');

        $total = count($results->get());
        $records = $results->skip($skip)->limit($limit)->orderBy($sortBy, $flow)->get();

        return ['records' => $records, 'total' => $total];
    }

    /**
     * Fetch transfer wise product movement
     *
     * @param  mixed $request
     * @return array $records, $total
     */
    public function fetchProductMovement(Request $request)
    {
        $skip = $request->get('skip');
        $flow = $request->get('flow');
        $limit = $request->get('limit');
        $search = $request->get('query');
        $sortBy = $request->get('sortBy');

        $toDate = $request->get('to');
        $fromDate = $request->get('from');

        $productId = $request->get('product_id');

        $query = DB::table('stock_transfers as st')
            ->where('stp.product_id', $productId)
            ->leftJoin('stock_transfer_products as stp', 'stp.stock_transfer_id', '=', 'st.id')
            ->leftJoin('products as pr', 'pr.id', '=', 'stp.product_id')
            ->leftJoin('godowns as tg', 'tg.id', '=', 'st.to_godown_id')
            ->leftJoin('godowns as fg', 'fg.id', '=', 'st.from_godown_id')
            ->leftJoin('transfer_types as tt', 'tt.id', '=', 'st.transfer_type_id');

        if (!is_null($fromDate) && !is_null($toDate)) {
            $query->whereDate('st.date', '<=', $toDate)->whereDate('st.date', '>=', $fromDate);
        }

        $results = $query->where(function ($query) use ($search) {
                $query->where('tt.name', 'like', '%' . $search . '%')
                    ->orWhere('fg.name', 'like', '%' . $search . '%')
                    ->orWhere('tg.name', 'like', '%' . $search . '%');
            })
            ->selectRaw('
                stp.quantity,
                stp.compound_quantity as compoundQuantity,
                pr.compound_unit as compoundUnit,
                pr.packing,
                st.id as id,
                st.date as date,
                st.transfer_type_id as ttid,
                pr.unit as unit,
                tg.name as toName,
                fg.name as fromName,
                tt.name as transferType
            ');

        $total = $results->count();
        $records = $results->skip($skip)->limit($limit)->orderBy($sortBy, $flow)->get();

        return ['records' => $records, 'total' => $total];
    }

    /**
     * Fetch transfer wise account movement
     *
     * @param  mixed $request
     * @return array $records, $total
     */
    public function fetchGodownMovement(Request $request)
    {
        $skip = $request->get('skip');
        $flow = $request->get('flow');
        $limit = $request->get('limit');
        $search = $request->get('query');
        $sortBy = $request->get('sortBy');

        $toDate = $request->get('to');
        $fromDate = $request->get('from');

        $accountId = $request->get('account_id');

        $query = DB::table('stock_transfers as st')
            ->leftJoin('stock_transfer_products as stp', 'stp.stock_transfer_id', '=', 'st.id')
            ->leftJoin('products as pr', 'pr.id', '=', 'stp.product_id')
            ->leftJoin('godowns as tg', 'tg.id', '=', 'st.to_godown_id')
            ->leftJoin('godowns as fg', 'fg.id', '=', 'st.from_godown_id')
            ->leftJoin('transfer_types as tt', 'tt.id', '=', 'st.transfer_type_id');

        if (!is_null($fromDate) && !is_null($toDate)) {
            $query->whereDate('st.date', '<=', $toDate)->whereDate('st.date', '>=', $fromDate);
        }

        $results = $query->where(function ($query) use ($accountId) {
                $query->where('st.to_godown_id', $accountId)
                    ->orWhere('st.from_godown_id', $accountId);
            })
            ->where(function ($query) use ($search) {
                $query->where('tt.name', 'like', '%' . $search . '%')
                ->orWhere('fg.name', 'like', '%' . $search . '%')
                ->orWhere('tg.name', 'like', '%' . $search . '%')
                ->orWhere('pr.name', 'like', '%' . $search . '%');
            })
            ->selectRaw('
                st.id as id,
                stp.quantity,
                stp.compound_quantity as compoundQuantity,
                pr.compound_unit as compoundUnit,
                pr.packing,
                st.date as date,
                st.from_godown_id as fromId,
                st.to_godown_id as toId,
                st.transfer_type_id as ttid,
                IF(st.from_godown_id = ?, tg.name, fg.name) as name,
                pr.id as productId,
                pr.unit as unit,
                pr.name as productName,
                pr.lot_number as lotNumber,
                tt.name as transferType
            ', [$accountId]);

        $total = $results->count();
        $records = $results->skip($skip)->limit($limit)->orderBy($sortBy, $flow)->get();

        return ['records' => $records, 'total' => $total];
    }

    /**
     * Fetch transfers via associated agents
     *
     * @param  mixed $request
     * @return array $records, $total
     */
    public function fetchAgentTransfers(Request $request)
    {
        $skip = $request->get('skip');
        $flow = $request->get('flow');
        $limit = $request->get('limit');
        $search = $request->get('query');
        $sortBy = $request->get('sortBy');

        $toDate = $request->get('to');
        $fromDate = $request->get('from');

        $agentId = $request->get('agent_id');

        $query = DB::table('stock_transfers as st')
            ->leftJoin('godowns as tg', 'tg.id', '=', 'st.to_godown_id')
            ->leftJoin('godowns as fg', 'fg.id', '=', 'st.from_godown_id')
            ->leftJoin('transfer_types as tt', 'tt.id', '=', 'st.transfer_type_id')
            ->where('st.agent_id', $agentId)
            ->where('st.agent_id', '!=', null);

        if (!is_null($fromDate) && !is_null($toDate)) {
            $query->whereDate('st.date', '<=', $toDate)->whereDate('st.date', '>=', $fromDate);
        }

        $results = $query->where(function ($query) use ($search) {
                $query->where('tt.name', 'like', '%' . $search . '%')
                ->orWhere('fg.name', 'like', '%' . $search . '%')
                ->orWhere('st.invoice_no', 'like', '%' . $search . '%')
                ->orWhere('tg.name', 'like', '%' . $search . '%');
            })
            ->selectRaw('
                st.id as id,
                st.date as date,
                st.invoice_no as invoiceNo,
                st.updated_at,
                st.transfer_type_id as ttid,
                tg.name as toName,
                fg.name as fromName,
                tt.name as transferType
            ');

        $total = $results->count();
        $records = $results->skip($skip)->limit($limit)->orderBy($sortBy, $flow)->get();

        return ['records' => $records, 'total' => $total];
    }

    /**
     * Purchase, sales and inter godown transfers
     *
     * @param  mixed $request
     * @return array $records, $total
     */
    public function fetchAllTransfers(Request $request)
    {
        $skip = $request->get('skip');
        $flow = $request->get('flow');
        $limit = $request->get('limit');
        $search = $request->get('query');
        $sortBy = $request->get('sortBy');

        $toDate = $request->get('to');
        $fromDate = $request->get('from');


        $query = DB::table('stock_transfers as st')
            ->leftJoin('godowns as tg', 'tg.id', '=', 'st.to_godown_id')
            ->leftJoin('godowns as fg', 'fg.id', '=', 'st.from_godown_id')
            ->leftJoin('transfer_types as tt', 'tt.id', '=', 'st.transfer_type_id');

        if (!is_null($fromDate) && !is_null($toDate)) {
            $query->whereDate('st.date', '<=', $toDate)->whereDate('st.date', '>=', $fromDate);
        }

        $results = $query->where(function ($query) use ($search) {
                $query->where('tt.name', 'like', '%' . $search . '%')
                ->orWhere('fg.name', 'like', '%' . $search . '%')
                ->orWhere('st.invoice_no', 'like', '%' . $search . '%')
                ->orWhere('tg.name', 'like', '%' . $search . '%');
            })
            ->selectRaw('
                st.id as id,
                st.invoice_no as invoiceNo,
                st.date as date,
                st.updated_at,
                st.transfer_type_id as ttid,
                tg.name as toName,
                fg.name as fromName,
                tt.name as transferType
            ');

        $total = $results->count();
        $records = $results->skip($skip)->limit($limit)->orderBy($sortBy, $flow)->get();

        return ['records' => $records, 'total' => $total];
    }
}
