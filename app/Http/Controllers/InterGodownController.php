<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StockTransferRequest;
use App\Services\Response\ResponseService;
use App\Services\Transfers\InterGodownService;
use App\Repositories\Transfers\InterGodownRepository;
use Illuminate\Support\Facades\DB;

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
            ->records(
                $this->interGodownRepository->fetchAll($request)
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
        $errors = $this->interGodownService->validateProducts($request);
        if (count($errors) > 0) return $this->responseService->error($errors);

        DB::transaction(function () use ($request) {
            $this->interGodownRepository->create($request, $this->interGodownService);
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
                $this->interGodownRepository->fetchOne($id)
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
                $this->interGodownRepository->fetchShowTransferProducts($purchaseId)
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
        $errors = $this->interGodownService->validateProducts($request);
        if (count($errors) > 0) return $this->responseService->error($errors);

        DB::transaction(function () use ($request, $id) {
            $this->interGodownRepository->update($request, $id, $this->interGodownService);
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
        $this->interGodownRepository->destroy($id);

        return $this->responseService->success();
    }
}
