<?php

namespace App\Http\Controllers\Autofill;

use App\Agent;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AgentAutofillController extends Controller
{
    /**
     * Fetch all records
     */
    public function all()
    {
        return Agent::selectRaw('id, CONCAT_WS(" - ", name, CONCAT("(", alias, ")")) as name')->get();
    }

    /**
     * Fetch all agents with transactions
     *
     * @return void
     */
    public function withTransactions()
    {
        return DB::table('stock_transfers')
            ->leftJoin('agents', 'agents.id', 'stock_transfers.agent_id')
            ->whereNotNull('stock_transfers.agent_id')
            ->select('agents.id', 'agents.name')
            ->orderBy('agents.name')
            ->get();
    }
}
