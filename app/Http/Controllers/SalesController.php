<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Transfers\SalesService;
use App\Services\Response\ResponseService;
use App\Http\Requests\StockTransferRequest;
use App\Repositories\Transfers\SalesRepository;
use Illuminate\Support\Facades\DB;

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
            ->records(
                $this->salesRepository->fetchAll($request)
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
        $errors = $this->salesService->validateProducts($request);
        if (count($errors) > 0) return $this->responseService->error($errors);

        DB::transaction(function () use ($request) {
            $this->salesRepository->create($request, $this->salesService);
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
                $this->salesRepository->fetchOne($id)
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
                $this->salesRepository->fetchShowTransferProducts($purchaseId)
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
        $errors = $this->salesService->validateProducts($request);
        if (count($errors) > 0) return $this->responseService->error($errors);

        DB::transaction(function () use ($request, $id) {
            $this->salesRepository->update($request, $id, $this->salesService);
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
        $this->salesRepository->destroy($id);

        return $this->responseService->success();
    }

    /**
     * New sale
     *
     * @return \Illuminate\Http\Response
     */
    public function new()
    {
        return $this->responseService
            ->record(
                $this->salesRepository->new()
            );
    }
}
