<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StockTransferRequest;
use App\Services\Response\ResponseService;
use App\Services\Transfers\PurchaseService;
use App\Repositories\Transfers\PurchaseRepository;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    /**
     * @var \App\Services\Response\ResponseService
     */
    protected $responseService;

    /**
     * @var \App\Services\Transfers\PurchaseService
     */
    protected $purchaseService;

    /**
     * @var \App\Repositories\Transfers\PurchaseRepository
     */
    protected $purchaseRepository;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->responseService = new ResponseService;
        $this->purchaseService = new PurchaseService;
        $this->purchaseRepository = new PurchaseRepository;
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
                $this->purchaseRepository->fetchAll($request)
            );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StockTransferRequest $request)
    {
        $errors = $this->purchaseService->validateProducts($request);
        if (count($errors) > 0) return $this->responseService->error($errors);

        DB::transaction(function () use ($request) {
            $this->purchaseRepository->create($request, $this->purchaseService);
        });

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
            ->record(
                $this->purchaseRepository->fetchOne($id)
            );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $purchaseId
     * @return \Illuminate\Http\Response
     */
    public function showTransferProducts($purchaseId)
    {
        return $this->responseService
            ->record(
                $this->purchaseRepository->fetchShowTransferProducts($purchaseId)
            );
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
        $errors = $this->purchaseService->validateProducts($request);
        if (count($errors) > 0) return $this->responseService->error($errors);

        DB::transaction(function () use ($request, $id) {
            $this->purchaseRepository->update($request, $id, $this->purchaseService);
        });

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
        $this->purchaseRepository->destroy($id);

        return $this->responseService->success();
    }
}
