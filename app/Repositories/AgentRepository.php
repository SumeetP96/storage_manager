<?php

namespace App\Repositories;

use App\Agent;
use App\StockTransfer;
use App\Traits\AgentTrait;
use Illuminate\Http\Request;

class AgentRepository
{
    use AgentTrait;

    /**
     * Fetch all records
     */
    public function fetchAll(Request $request)
    {
        return [
            'total' => $this->allRecords($request)->count(),

            'records' => $this->allRecords($request)
                ->skip($request->skip)->limit($request->limit)->get(),
        ];
    }

    /**
     * Fetch a single record by id
     */
    public function fetchOne($id)
    {
        return Agent::find($id);
    }

    /**
     * Fetch records for autocomplete
     */
    public function fetchAutocompletes()
    {
        return Agent::selectRaw("id, CONCAT_WS(' - ', name, alias) AS name")->get();
    }

    /**
     * Fetch records for autocomplete
     */
    public function fetchAutocompletesWithTransfer()
    {
        $agentIds = [];
        $agentData = StockTransfer::distinct()->where('agent_id', '!=', NULL)->get(['agent_id']);

        foreach($agentData as $data) array_push($agentIds, $data->agent_id);

        return Agent::whereIn('id', $agentIds)->selectRaw("id, CONCAT_WS(' - ', name, alias) AS name")->get();
    }

    /**
     * Store record in database
     */
    public function create(Request $request)
    {
        return Agent::create([
            'name'      => $request->name,
            'alias'     => ($request->alias != '') ? strtoupper($request->alias) : NULL,
            'contact_1' => $request->contact_1,
            'contact_2' => $request->contact_2,
            'email'     => strtolower($request->email),
            'remarks'   => $request->remarks
        ])->id;
    }

    /**
     * Update record in database
     */
    public function update(Request $request, $id)
    {
        Agent::find($id)->update([
            'name'      => $request->name,
            'alias'     => ($request->alias != '') ? strtoupper($request->alias) : NULL,
            'contact_1' => $request->contact_1,
            'contact_2' => $request->contact_2,
            'email'     => strtolower($request->email),
            'remarks'   => $request->remarks
        ]);
    }

    /**
     * Delete record from database
     */
    public function destroy($id)
    {
        Agent::find($id)->delete();
    }
}
