<?php

namespace App\Repositories;

use App\Godown;
use App\StockTransfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GodownRepository
{
    /**
     * Fetch all records
     */
    public function fetchAll(Request $request)
    {
        $is_account = (bool) $request->get('is_account');

        $search = $request->get('query');
        $limit = $request->get('limit');
        $skip = $request->get('skip');
        $sortBy = $request->get('sortBy');
        $flow = $request->get('flow');

        $results = DB::table('godowns')
            ->where('is_account', $is_account)
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('alias', 'like', '%' . $search . '%')
                    ->orWhere('contact_1', 'like', '%' . $search . '%')
                    ->orWhere('contact_2', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('remarks', 'like', '%' . $search . '%');
            });

        $total = $results->count();
        $records = $results->skip($skip)->limit($limit)->orderBy($sortBy, $flow)->get();

        return ['records' => $records, 'total' => $total];
    }

    /**
     * Fetch a single record by id
     */
    public function fetchOne($id)
    {
        return Godown::find($id);
    }

    /**
     * Fetch records for autocomplete
     */
    public function fetchAutocompletes($type)
    {
        $is_account = (bool) $type;

        return Godown::where('is_account', $is_account)
            ->selectRaw("id, CONCAT_WS(' - ', name, alias) AS name")
            ->orderBy('name')
            ->get();
    }

    /**
     * Fetch records for autocomplete with Stocks
     */
    public function fetchAutocompletesWithStock()
    {
        return DB::table('godowns as gd')
            ->leftJoin('godown_products_stocks as gps', 'gps.godown_id', '=', 'gd.id')
            ->where('gd.is_account', FALSE)
            ->selectRaw("gd.id, CONCAT_WS(' - ', gd.name, gd.alias) AS name")
            ->orderBy('name')
            ->get();
    }

    /**
     * Fetch records for autocomplete with Stocks
     */
    public function fetchAutocompletesWithTransfer()
    {
        $godownIds = [];
        $fromData = StockTransfer::distinct()->get(['from_godown_id']);
        $toData = StockTransfer::distinct()->get(['to_godown_id']);

        foreach ($fromData as $data) array_push($godownIds, $data->from_godown_id);
        foreach ($toData as $data) array_push($godownIds, $data->to_godown_id);

        $godownIds = array_unique($godownIds);

        return Godown::where('is_account', TRUE)
            ->whereIn('id', $godownIds)
            ->selectRaw("id, CONCAT_WS(' - ', name, alias) AS name")
            ->orderBy('name')
            ->get();
    }

    /**
     * Fetch selected record details
     */
    public function details($id)
    {
        return Godown::where('id', $id)
            ->select('address', 'contact_1', 'contact_2')
            ->first();
    }

    /**
     * Store record in database
     */
    public function create(Request $request)
    {
        return Godown::create([
            'is_account'    => $request->is_account,
            'name'          => $request->name,
            'alias'         => ($request->alias != '') ? strtoupper($request->alias) : NULL,
            'address'       => $request->address,
            'contact_1'     => $request->contact_1,
            'contact_2'     => $request->contact_2,
            'email'         => strtolower($request->email),
            'remarks'       => $request->remarks
        ])->id;
    }

    /**
     * Update record in database
     */
    public function update(Request $request, $id)
    {
        Godown::find($id)->update([
            'name'      => $request->name,
            'alias'     => ($request->alias != '') ? strtoupper($request->alias) : NULL,
            'address'   => $request->address,
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
        Godown::find($id)->delete();
    }
}
