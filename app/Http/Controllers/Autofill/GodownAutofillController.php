<?php

namespace App\Http\Controllers\Autofill;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GodownAutofillController extends Controller
{
    /**
     * Fetch to godowns with transactions
     *
     * @param  integer $transferType
     * @return void
     */
    public function toWithTransactions($transferType)
    {
        return DB::table('stock_transfers as st')
            ->where('st.transfer_type_id', $transferType)
            ->leftJoin('godowns as gd', 'gd.id', '=', 'st.to_godown_id')
            ->select('gd.id', 'gd.name')
            ->orderBy('gd.name')
            ->get();
    }

    /**
     * Fetch from godowns with transactions
     *
     * @param  integer $transferType
     * @return void
     */
    public function fromWithTransactions($transferType)
    {
        return DB::table('stock_transfers as st')
            ->where('st.transfer_type_id', $transferType)
            ->leftJoin('godowns as gd', 'gd.id', '=', 'st.from_godown_id')
            ->select('gd.id', 'gd.name')
            ->orderBy('gd.name')
            ->get();
    }

    public function withStock()
    {
        return DB::table('godown_products_stocks as gps')
            ->leftJoin('godowns as gd', 'gps.godown_id', '=', 'gd.id')
            ->where('gps.current_stock', '>', 0)
            ->select('gd.name', 'gd.id')
            ->orderBy('gd.name')
            ->get();
    }

    public function withStockProducts()
    {
        return DB::table('godown_products_stocks as gps')
            ->leftJoin('products as pr', 'gps.product_id', '=', 'pr.id')
            ->where('gps.current_stock', '>', 0)
            ->select('pr.name', 'pr.id')
            ->orderBy('pr.name')
            ->get();
    }
}