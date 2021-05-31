<?php

namespace App\Repositories;

use App\Traits\Reports\Movement\GodownMovementTrait;
use App\Traits\Reports\Movement\ProductMovementTrait;
use App\Traits\Reports\Stock\GodownProductStockTrait;
use App\Traits\Reports\Stock\ProductLotStockTrait;
use App\Traits\Reports\Transfers\AgentTransfersTrait;
use App\Traits\Reports\Transfers\AllTransfersTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportRepository
{
    use AllTransfersTrait;
    use AgentTransfersTrait;
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
                $query->where('pr.lot_number', 'like', '%' . $search . '%');
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
        return [
            'total' => $this->allAgentTransfers($request, $request->agent_id)->count(),

            'records' => $this->allAgentTransfers($request, $request->agent_id)
                ->skip($request->skip)->limit($request->limit)->get(),
        ];
    }

    /**
     * Purchase, sales and inter godown transfers
     *
     * @param  mixed $request
     * @return array $records, $total
     */
    public function fetchAllTransfers(Request $request)
    {
        return [
            'total' => $this->allTransfers($request)->count(),

            'records' => $this->allTransfers($request)
                ->skip($request->skip)->limit($request->limit)->get(),
        ];
    }
}
