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

        return DB::table('products')
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('alias', 'like', '%' . $search . '%')
                    ->orWhere('remarks', 'like', '%' . $search . '%')
                    ->orWhere('lot_number', 'like', '%' . $search . '%');
            })->orderBy($sortBy, $flow);
    }
}
