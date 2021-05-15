<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateGodownRequest;
use App\Http\Requests\CreateGodownRequest;
use App\Repositories\GodownRepository;
use App\Services\Response\ResponseService;

class GodownController extends Controller
{
    /**
     * @var \App\Services\Response\ResponseService
     */
    protected $responseService;

    /**
     * @var \App\Repositories\GodownRepository
     */
    protected $godownRepository;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->responseService = new ResponseService;
        $this->godownRepository = new GodownRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $this->responseService
            ->records(
                $this->godownRepository->fetchAll($request)
            );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateGodownRequest $request)
    {
        return $this->responseService
            ->success(
                $this->godownRepository->create($request)
            );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->responseService
            ->record(
                $this->godownRepository->fetchOne($id)
            );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateGodownRequest $request, $id)
    {
        $this->godownRepository->update($request, $id);

        return $this->responseService->success();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->godownRepository->destroy($id);

        return $this->responseService->success();
    }

    /**
     * Fetch records for autocomplete
     */
    public function autocomplete($type)
    {
        return $this->responseService
            ->autocomplete(
                $this->godownRepository->fetchAutocompletes($type)
            );
    }

    /**
     * Fetch records for autocomplete with stock
     */
    public function autocompleteWithStock()
    {
        return $this->responseService
            ->autocomplete(
                $this->godownRepository->fetchAutocompletesWithStock()
            );
    }

    /**
     * Fetch records for autocomplete with transfers
     */
    public function autocompleteWithTransfer()
    {
        return $this->responseService
            ->autocomplete(
                $this->godownRepository->fetchAutocompletesWithTransfer()
            );
    }

    /**
     * Fetch selected record details
     */
    public function details($id)
    {
        return $this->godownRepository->details($id);
    }
}
