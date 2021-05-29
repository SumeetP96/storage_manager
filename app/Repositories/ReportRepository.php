<?php

namespace App\Repositories;

use App\Traits\Reports\Movement\GodownMovementTrait;
use App\Traits\Reports\Movement\ProductMovementTrait;
use App\Traits\Reports\Stock\GodownProductStockTrait;
use App\Traits\Reports\Stock\ProductLotStockTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportRepository
{
    use GodownMovementTrait;
    use ProductMovementTrait;
    use ProductLotStockTrait;
    use GodownProductStockTrait;

    /**
     * Fetch product wise total stock
     *
     * @param  mixed $request
     * @return array $records, $total
     */
    public function fetchProductsStock(Request $request)
    {
        $flow = $request->get('flow');
        $skip = $request->get('skip');
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

        $total = $results->count();
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
        return [
            'total' => $this->allGodownProductStock($request)->count(),

            'records' => $this->allGodownProductStock($request)
                ->skip($request->skip)->limit($request->limit)->get(),
        ];
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
        return [
            'total' => $this->allProductLotStock($request)->count(),

            'records' => $this->allProductLotStock($request)
                ->skip($request->skip)->limit($request->limit)->get(),
        ];
    }

    /**
     * Fetch transfer wise product movement
     *
     * @param  mixed $request
     * @return array $records, $total
     */
    public function fetchProductMovement(Request $request)
    {
        return [
            'total' => $this->allProductMovement($request, $request->product_id)->count(),

            'records' => $this->allProductMovement($request, $request->product_id)
                ->skip($request->skip)->limit($request->limit)->get(),
        ];
    }

    /**
     * Fetch transfer wise account movement
     *
     * @param  mixed $request
     * @return array $records, $total
     */
    public function fetchGodownMovement(Request $request)
    {
        return [
            'total' => $this->allGodownMovement($request, $request->account_id)->count(),

            'records' => $this->allGodownMovement($request, $request->account_id)
                ->skip($request->skip)->limit($request->limit)->get(),
        ];
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
