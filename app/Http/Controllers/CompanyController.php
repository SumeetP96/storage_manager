<?php

namespace App\Http\Controllers;

use App\Company;
use Illuminate\Http\Request;
use App\Services\Response\ResponseService;

class CompanyController extends Controller
{
    /**
     * @var \App\Services\Response\ResponseService
     */
    private $responseService;

    public function __construct()
    {
        $this->responseService = new ResponseService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Company::find(1);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $request->validate([
            'name'      => 'required',
            'email'     => 'nullable|email'
        ]);

        Company::find(1)->update([
            'name'      => $request->name,
            'address'   => $request->address,
            'contact_1' => $request->contact_1,
            'contact_2' => $request->contact_2,
            'email'     => $request->email
        ]);

        return $this->responseService->success();
    }
}
