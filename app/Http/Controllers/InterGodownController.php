<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StockTransferRequest;
use App\Services\Response\ResponseService;
use App\Services\Transfers\InterGodownService;
use App\Repositories\Transfers\InterGodownRepository;

class InterGodownController extends Controller
{
    /**
     * @var \App\Services\Response\ResponseService
     */
    protected $responseService;

    /**
     * @var \App\Services\Transfers\InterGodownService
     */
    protected $interGodownService;

    /**
     * @var \App\Repositories\Transfers\InterGodownRepository
     */
    protected $interGodownRepository;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->responseService = new ResponseService;
        $this->interGodownService = new InterGodownService;
        $this->interGodownRepository = new InterGodownRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $this->responseService
            ->records($this->interGodownRepository->fetchAll($request));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StockTransferRequest $request)
    {
        if (! $this->interGodownService->validateQuantity($request)) {
            return $this->responseService->error([
                'quantity'  => ['Quantity exceeds stock.']
            ]);
        }

        $this->interGodownRepository->create($request);

        if ($existingGPS = $this->interGodownService->checkExistingGPS($request)) {
            $this->interGodownRepository->updateGPS($existingGPS, $request);
        } else {
            $this->interGodownRepository->createGPS($request);
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
            ->record($this->interGodownRepository->fetchOne($id));
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
        if (! $this->interGodownService->validateQuantity($request)) {
            return $this->responseService->error([
                'quantity'  => ['Quantity exceeds stock.']
            ]);
        }

        $this->interGodownRepository->update($request, $id);

        if ($existingGPS = $this->interGodownService->checkExistingGPS($request)) {
            $this->interGodownRepository->updateGPS($existingGPS, $request);
        } else {
            $this->interGodownRepository->createGPS($request);
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
        $this->interGodownRepository->destroy($id);

        return $this->responseService->success();
    }
}
