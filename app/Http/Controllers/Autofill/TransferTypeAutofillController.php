<?php

namespace App\Http\Controllers\Autofill;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class TransferTypeAutofillController extends Controller
{
    public function usedByProduct($productId)
    {
        return DB::table('stock_transfers as st')
            ->leftJoin('transfer_types as tt', 'tt.id', 'st.transfer_type_id')
            ->leftJoin('stock_transfer_products as stp', 'stp.stock_transfer_id', '=', 'st.id')
            ->distinct()
            ->where('stp.product_id', $productId)
            ->select('tt.name', 'tt.id')
            ->get(['transfer_type_id']);
    }

    public function usedByGodown($godownId)
    {
        return DB::table('stock_transfers as st')
            ->leftJoin('transfer_types as tt', 'tt.id', '=', 'st.transfer_type_id')
            ->where(function ($query) use ($godownId) {
                $query->where('st.to_godown_id', $godownId)
                    ->orWhere('st.from_godown_id', $godownId);
            })
            ->distinct()
            ->select('tt.name', 'tt.id')
            ->get(['tt.id']);
    }

    public function usedByAgent($agentId)
    {
        return DB::table('stock_transfers as st')
            ->leftJoin('transfer_types as tt', 'tt.id', '=', 'st.transfer_type_id')
            ->where('st.agent_id', $agentId)
            ->distinct()
            ->select('tt.name', 'tt.id')
            ->get(['tt.id']);
    }

    public function all()
    {
        return DB::table('stock_transfers as st')
            ->leftJoin('transfer_types as tt', 'tt.id', '=', 'st.transfer_type_id')
            ->distinct()
            ->select('tt.name', 'tt.id')
            ->get(['tt.id']);
    }
}
