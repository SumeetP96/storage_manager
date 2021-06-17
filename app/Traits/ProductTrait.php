<?php

namespace App\Traits;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

trait ProductTrait
{
    /**
     * Return all records as collection
     *
     * @param  \Illuminate\Http\Request $request
     * @return Object
     */
    public function allRecords(Request $request)
    {
        $flow = $request->get('flow');
        $search = $request->get('query');
        $sortBy = $request->get('sortBy');

        $query = DB::table('products');

        return $this->getFilteredQuery($query, $request)
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('alias', 'like', '%' . $search . '%')
                    ->orWhere('unit', 'like', '%' . $search . '%')
                    ->orWhere('packing', 'like', '%' . $search . '%')
                    ->orWhere('compound_unit', 'like', '%' . $search . '%')
                    ->orWhere('remarks', 'like', '%' . $search . '%');
            })->orderBy($sortBy, $flow);
    }

    /**
     * Get filtered query
     *
     * @param  mixed $query
     * @param  mixed $request
     * @return Object
     */
    public function getFilteredQuery($query, Request $request)
    {
        // Alias filters with / without
        $alias = $request->get('alias');
        if ($alias == 'with') $query->whereNotNull('alias');
        if ($alias == 'without') $query->where(function ($query) {
            $query->whereNull('alias')->orWhere('alias', '');
        });

        // Unit filters only / except
        $unitOnly = $request->get('unitOnly');
        if (!is_null($unitOnly) > 0) $query->whereIn('unit', explode(',', $unitOnly));
        $unitExcept = $request->get('unitExcept');
        if (!is_null($unitExcept) > 0) $query->whereNotIn('unit', explode(',', $unitExcept));

        // Compound Unit filters only / except
        $compound_unitOnly = $request->get('compound_unitOnly');
        if (!is_null($compound_unitOnly) > 0) $query->whereIn('compound_unit', explode(',', $compound_unitOnly));
        $compound_unitExcept = $request->get('compound_unitExcept');
        if (!is_null($compound_unitExcept) > 0) $query->whereNotIn('compound_unit', explode(',', $compound_unitExcept));

        // Less than greater than
        $packingLt = $request->get('packingLt');
        if (!is_null($packingLt)) $query->where('packing', '<', (int) $packingLt * 100);
        $packingGt = $request->get('packingGt');
        if (!is_null($packingGt)) $query->where('packing', '>', (int) $packingGt * 100);

        // Updated at date range
        $updatedFromDate = $request->get('updatedFrom');
        $updatedToDate = $request->get('updatedTo');
        if (!is_null($updatedFromDate) && !is_null($updatedToDate)) {
            $query->whereDate('updated_at', '<=', $updatedToDate)->whereDate('updated_at', '>=', $updatedFromDate);
        }

        // Created at date range
        $createdFromDate = $request->get('createdFrom');
        $createdToDate = $request->get('createdTo');
        if (!is_null($createdFromDate) && !is_null($createdToDate)) {
            $query->whereDate('created_at', '<=', $createdToDate)->whereDate('created_at', '>=', $createdFromDate);
        }

        return $query;
    }
}
