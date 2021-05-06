<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Transfers\SalesService;
use App\Services\Response\ResponseService;
use App\Http\Requests\StockTransferRequest;
use App\Repositories\Transfers\SalesRepository;

class SalesController extends Controller
{
    /**
     * @var \App\Services\Response\ResponseService
     */
    protected $responseService;

    /**
     * @var \App\Services\Transfers\SalesService
     */
    protected $salesService;

    /**
     * @var \App\Repositories\Transfers\SalesRepository
     */
    protected $salesRepository;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->responseService = new ResponseService;
        $this->salesService = new SalesService;
        $this->salesRepository = new SalesRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $this->responseService
            ->records($this->salesRepository->fetchAll($request));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StockTransferRequest $request)
    {
        $this->salesRepository->create($request);

        if ($existingGPS = $this->salesService->checkExistingGPS($request)) {
            $this->salesRepository->updateGPS($existingGPS, $request);
        } else {
            $this->salesRepository->createGPS($request);
        }

        return $this->responseService->success();
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
            ->record($this->salesRepository->fetchOne($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StockTransferRequest $request, $id)
    {
        if (! $this->salesService->validateQuantity($request)) {
            return $this->responseService->error([
                'quantity'  => ['Quantity exceeds stock.']
            ]);
        }

        $this->salesRepository->update($request, $id);

        if ($existingGPS = $this->salesService->checkExistingGPS($request)) {
            $this->salesRepository->updateGPS($existingGPS, $request);
        } else {
            $this->salesRepository->createGPS($request);
        }

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
        $this->salesRepository->destroy($id);

        return $this->responseService->success();
    }
}
