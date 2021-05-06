<?php

namespace App\Repositories;

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

        $records = DB::table('godown_products_stocks as gps')
            ->leftJoin('products as pr', 'gps.product_id', '=', 'pr.id')
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('unit', 'like', '%' . $search . '%');
            })
            ->selectRaw('
                pr.unit as unit,
                pr.name as name,
                sum(gps.current_stock) as stock
            ')
            ->groupBy('name', 'unit')
            ->skip($skip)
            ->limit($limit)
            ->orderBy($sortBy, $flow);

        $records = $records->get();
        $total = $records->count();

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

        $records = DB::table('godown_products_stocks as gps')
            ->leftJoin('godowns as gd', 'gps.godown_id', '=', 'gd.id')
            ->leftJoin('products as pr', 'gps.product_id', '=', 'pr.id')
            ->where(function ($query) use ($search) {
                $query->where('pr.name', 'like', '%' . $search . '%')
                    ->orWhere('gd.name', 'like', '%' . $search . '%')
                    ->orWhere('pr.lot_number', 'like', '%' . $search . '%');
            })
            ->select(
                'gps.updated_at',
                'gps.created_at',
                'gps.current_stock as currentStock',
                'gd.name as godownName',
                'pr.name as productName',
                'pr.unit as productUnit',
                'pr.lot_number as productLotNumber'
            )
            ->skip($skip)
            ->limit($limit)
            ->orderBy($sortBy, $flow);

        $records = $records->get();
        $total = $records->count();

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

        $records = DB::table('godown_products_stocks as gps')
            ->leftJoin('products as pr', 'gps.product_id', '=', 'pr.id')
            ->where(function ($query) use ($search) {
                $query->where('lot_number', 'like', '%' . $search . '%')
                    ->orWhere('unit', 'like', '%' . $search . '%');
            })
            ->selectRaw('
                pr.unit as unit,
                pr.lot_number as lotNumber,
                sum(gps.current_stock) as stock
            ')
            ->groupBy('lotNumber', 'unit')
            ->skip($skip)
            ->limit($limit)
            ->orderBy($sortBy, $flow);

        $records = $records->get();
        $total = $records->count();

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

        $records = DB::table('godown_products_stocks as gps')
            ->leftJoin('products as pr', 'gps.product_id', '=', 'pr.id')
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('lot_number', 'like', '%' . $search . '%')
                    ->orWhere('unit', 'like', '%' . $search . '%');
            })
            ->selectRaw('
                pr.unit as unit,
                pr.lot_number as lotNumber,
                pr.name as name,
                sum(gps.current_stock) as stock
            ')
            ->groupBy('lotNumber', 'unit', 'name')
            ->skip($skip)
            ->limit($limit)
            ->orderBy($sortBy, $flow);

        $records = $records->get();
        $total = $records->count();

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

        // If no date range selected
        if (!is_null($fromDate) && !is_null($toDate)) {
            $records = DB::table('stock_transfers as st')
                ->where('st.product_id', $productId)
                ->whereDate('st.date', '<=', $toDate)
                ->whereDate('st.date', '>=', $fromDate)
                ->leftJoin('products as pr', 'pr.id', '=', 'st.product_id')
                ->leftJoin('godowns as tg', 'tg.id', '=', 'st.to_godown_id')
                ->leftJoin('godowns as fg', 'fg.id', '=', 'st.from_godown_id')
                ->leftJoin('transfer_types as tt', 'tt.id', '=', 'st.transfer_type_id')
                ->where(function ($query) use ($search) {
                    $query->where('tt.name', 'like', '%' . $search . '%')
                        ->orWhere('fg.name', 'like', '%' . $search . '%')
                        ->orWhere('tg.name', 'like', '%' . $search . '%');
                })
                ->selectRaw('
                    st.quantity,
                    st.id as id,
                    st.date as date,
                    st.transfer_type_id as ttid,
                    pr.unit as unit,
                    tg.name as toName,
                    fg.name as fromName,
                    tt.name as transferType
                ')
                ->skip($skip)
                ->limit($limit)
                ->orderBy($sortBy, $flow);
        } else {
            $records = DB::table('stock_transfers as st')
                ->where('st.product_id', $productId)
                ->leftJoin('products as pr', 'pr.id', '=', 'st.product_id')
                ->leftJoin('godowns as tg', 'tg.id', '=', 'st.to_godown_id')
                ->leftJoin('godowns as fg', 'fg.id', '=', 'st.from_godown_id')
                ->leftJoin('transfer_types as tt', 'tt.id', '=', 'st.transfer_type_id')
                ->where(function ($query) use ($search) {
                    $query->where('tt.name', 'like', '%' . $search . '%')
                        ->orWhere('fg.name', 'like', '%' . $search . '%')
                        ->orWhere('tg.name', 'like', '%' . $search . '%');
                })
                ->selectRaw('
                    st.quantity,
                    st.id as id,
                    st.date as date,
                    st.transfer_type_id as ttid,
                    pr.unit as unit,
                    tg.name as toName,
                    fg.name as fromName,
                    tt.name as transferType
                ')
                ->skip($skip)
                ->limit($limit)
                ->orderBy($sortBy, $flow);
        }

        $records = $records->get();
        $total = $records->count();

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

        // If no date range selected
        if (!is_null($fromDate) && !is_null($toDate)) {
            $records = DB::table('stock_transfers as st')
                ->leftJoin('products as pr', 'pr.id', '=', 'st.product_id')
                ->leftJoin('godowns as tg', 'tg.id', '=', 'st.to_godown_id')
                ->leftJoin('godowns as fg', 'fg.id', '=', 'st.from_godown_id')
                ->leftJoin('transfer_types as tt', 'tt.id', '=', 'st.transfer_type_id')
                ->whereDate('st.date', '<=', $toDate)
                ->whereDate('st.date', '>=', $fromDate)
                ->where(function ($query) use ($accountId) {
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
                    st.quantity,
                    st.date as date,
                    st.transfer_type_id as ttid,
                    IF(st.from_godown_id = ?, tg.name, fg.name) as name,
                    pr.unit as unit,
                    pr.name as productName,
                    pr.lot_number as lotNumber,
                    tt.name as transferType
                ', [$accountId])
                ->skip($skip)
                ->limit($limit)
                ->orderBy($sortBy, $flow);
        } else {
            $records = DB::table('stock_transfers as st')
                ->leftJoin('products as pr', 'pr.id', '=', 'st.product_id')
                ->leftJoin('godowns as tg', 'tg.id', '=', 'st.to_godown_id')
                ->leftJoin('godowns as fg', 'fg.id', '=', 'st.from_godown_id')
                ->leftJoin('transfer_types as tt', 'tt.id', '=', 'st.transfer_type_id')
                ->where(function ($query) use ($accountId) {
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
                    st.quantity,
                    st.date as date,
                    st.transfer_type_id as ttid,
                    IF(st.from_godown_id = ?, tg.name, fg.name) as name,
                    pr.unit as unit,
                    pr.name as productName,
                    pr.lot_number as lotNumber,
                    tt.name as transferType
                ', [$accountId])
                ->skip($skip)
                ->limit($limit)
                ->orderBy($sortBy, $flow);
        }

        $records = $records->get();
        $total = $records->count();

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

        // If no date range selected
        if (!is_null($fromDate) && !is_null($toDate)) {
            $records = DB::table('stock_transfers as st')
                ->leftJoin('products as pr', 'pr.id', '=', 'st.product_id')
                ->leftJoin('godowns as tg', 'tg.id', '=', 'st.to_godown_id')
                ->leftJoin('godowns as fg', 'fg.id', '=', 'st.from_godown_id')
                ->leftJoin('transfer_types as tt', 'tt.id', '=', 'st.transfer_type_id')
                ->whereDate('st.date', '<=', $toDate)
                ->whereDate('st.date', '>=', $fromDate)
                ->where('agent_id', $agentId)
                ->where('agent_id', '!=', null)
                ->where(function ($query) use ($search) {
                    $query->where('tt.name', 'like', '%' . $search . '%')
                    ->orWhere('fg.name', 'like', '%' . $search . '%')
                    ->orWhere('tg.name', 'like', '%' . $search . '%')
                    ->orWhere('pr.name', 'like', '%' . $search . '%');
                })
                ->selectRaw('
                    st.id as id,
                    st.quantity,
                    st.date as date,
                    st.transfer_type_id as ttid,
                    tg.name as toName,
                    fg.name as fromName,
                    pr.unit as unit,
                    pr.name as productName,
                    pr.lot_number as lotNumber,
                    tt.name as transferType
                ')
                ->skip($skip)
                ->limit($limit)
                ->orderBy($sortBy, $flow);
        } else {
            $records = DB::table('stock_transfers as st')
                ->leftJoin('products as pr', 'pr.id', '=', 'st.product_id')
                ->leftJoin('godowns as tg', 'tg.id', '=', 'st.to_godown_id')
                ->leftJoin('godowns as fg', 'fg.id', '=', 'st.from_godown_id')
                ->leftJoin('transfer_types as tt', 'tt.id', '=', 'st.transfer_type_id')
                ->where('agent_id', $agentId)
                ->where('agent_id', '!=', null)
                ->where(function ($query) use ($search) {
                    $query->where('tt.name', 'like', '%' . $search . '%')
                    ->orWhere('fg.name', 'like', '%' . $search . '%')
                    ->orWhere('tg.name', 'like', '%' . $search . '%')
                    ->orWhere('pr.name', 'like', '%' . $search . '%');
                })
                ->selectRaw('
                    st.id as id,
                    st.quantity,
                    st.date as date,
                    st.transfer_type_id as ttid,
                    tg.name as toName,
                    fg.name as fromName,
                    pr.unit as unit,
                    pr.name as productName,
                    pr.lot_number as lotNumber,
                    tt.name as transferType
                ')
                ->skip($skip)
                ->limit($limit)
                ->orderBy($sortBy, $flow);
        }

        $records = $records->get();
        $total = $records->count();

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

        // If no date range selected
        if (!is_null($fromDate) && !is_null($toDate)) {
            $records = DB::table('stock_transfers as st')
                ->leftJoin('products as pr', 'pr.id', '=', 'st.product_id')
                ->leftJoin('godowns as tg', 'tg.id', '=', 'st.to_godown_id')
                ->leftJoin('godowns as fg', 'fg.id', '=', 'st.from_godown_id')
                ->leftJoin('transfer_types as tt', 'tt.id', '=', 'st.transfer_type_id')
                ->whereDate('st.date', '<=', $toDate)
                ->whereDate('st.date', '>=', $fromDate)
                ->where(function ($query) use ($search) {
                    $query->where('tt.name', 'like', '%' . $search . '%')
                    ->orWhere('fg.name', 'like', '%' . $search . '%')
                    ->orWhere('tg.name', 'like', '%' . $search . '%')
                    ->orWhere('pr.name', 'like', '%' . $search . '%');
                })
                ->selectRaw('
                    st.id as id,
                    st.quantity,
                    st.date as date,
                    st.transfer_type_id as ttid,
                    tg.name as toName,
                    fg.name as fromName,
                    pr.unit as unit,
                    pr.name as productName,
                    pr.lot_number as lotNumber,
                    tt.name as transferType
                ')
                ->skip($skip)
                ->limit($limit)
                ->orderBy($sortBy, $flow);
        } else {
            $records = DB::table('stock_transfers as st')
                ->leftJoin('products as pr', 'pr.id', '=', 'st.product_id')
                ->leftJoin('godowns as tg', 'tg.id', '=', 'st.to_godown_id')
                ->leftJoin('godowns as fg', 'fg.id', '=', 'st.from_godown_id')
                ->leftJoin('transfer_types as tt', 'tt.id', '=', 'st.transfer_type_id')
                ->where(function ($query) use ($search) {
                    $query->where('tt.name', 'like', '%' . $search . '%')
                    ->orWhere('fg.name', 'like', '%' . $search . '%')
                    ->orWhere('tg.name', 'like', '%' . $search . '%')
                    ->orWhere('pr.name', 'like', '%' . $search . '%');
                })
                ->selectRaw('
                    st.id as id,
                    st.quantity,
                    st.date as date,
                    st.transfer_type_id as ttid,
                    tg.name as toName,
                    fg.name as fromName,
                    pr.unit as unit,
                    pr.name as productName,
                    pr.lot_number as lotNumber,
                    tt.name as transferType
                ')
                ->skip($skip)
                ->limit($limit)
                ->orderBy($sortBy, $flow);
        }

        $records = $records->get();
        $total = $records->count();

        return ['records' => $records, 'total' => $total];
    }
}
