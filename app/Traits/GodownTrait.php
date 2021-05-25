<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

trait GodownTrait
{
    /**
     * Return all records as collection
     *
     * @param  \Illuminate\Http\Request $request
     * @return Object
     */
    public function allRecords(Request $request)
    {
        $is_account = (bool) $request->get('is_account');

        $flow = $request->get('flow');
        $search = $request->get('query');
        $sortBy = $request->get('sortBy');

        $query = DB::table('godowns')->where('is_account', $is_account);

        // Alias filters with / without
        $alias = $request->get('alias');
        if ($alias == 'with') $query->whereNotNull('alias');
        if ($alias == 'without') $query->whereNull('alias')->orWhere('alias', '');

        // Contact 1 filters with / without
        $contact_1 = $request->get('contact_1');
        if ($contact_1 == 'with') $query->whereNotNull('contact_1');
        if ($contact_1 == 'without') $query->whereNull('contact_1')->orWhere('contact_1', '');

        // Contact 2 filters with / without
        $contact_2 = $request->get('contact_2');
        if ($contact_2 == 'with') $query->whereNotNull('contact_2');
        if ($contact_2 == 'without') $query->whereNull('contact_2')->orWhere('contact_2', '');

        // Email filters with / without
        $email = $request->get('email');
        if ($email == 'with') $query->whereNotNull('email');
        if ($email == 'without') $query->whereNull('email')->orWhere('email', '');


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

        return $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                ->orWhere('alias', 'like', '%' . $search . '%')
                ->orWhere('contact_1', 'like', '%' . $search . '%')
                ->orWhere('contact_2', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%')
                ->orWhere('remarks', 'like', '%' . $search . '%');
            })->orderBy($sortBy, $flow);

    }
}
