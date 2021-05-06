<?php

namespace App\Repositories;

use App\Agent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AgentRepository
{
    /**
     * Fetch all records
     */
    public function fetchAll(Request $request)
    {
        $search = $request->get('query');
        $limit = $request->get('limit');
        $skip = $request->get('skip');
        $sortBy = $request->get('sortBy');
        $flow = $request->get('flow');

        $records = DB::table('agents')
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('alias', 'like', '%' . $search . '%')
                    ->orWhere('contact_1', 'like', '%' . $search . '%')
                    ->orWhere('contact_2', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('remarks', 'like', '%' . $search . '%');
            })->limit($limit)->skip($skip)
            ->orderBy($sortBy, $flow);

        return ['records' => $records->get(), 'total' => $records->count()];
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
