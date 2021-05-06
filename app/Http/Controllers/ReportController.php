<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\ReportRepository;
use App\Services\Response\ResponseService;

class ReportController extends Controller
{
    /**
     * @var \App\Services\Response\ResponseService
     */
    private $responseService;

    /**
     *
     * @var \App\Repositories\ReportRepository
     */
    private $reportRepository;


    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->responseService = new ResponseService;
        $this->reportRepository = new ReportRepository;
    }

    /**
     * Product wise total stock
     *
     * @param  mixed $request
     * @return \Illuminate\Http\Response
     */
    public function productsStock(Request $request)
    {
        return $this->responseService->records(
            $this->reportRepository->fetchProductsStock($request)
        );
    }

    /**
     * Godown wise product stock
     *
     * @param  mixed $request
     * @return \Illuminate\Http\Response
     */
    public function godownProductsStock(Request $request)
    {
        return $this->responseService
            ->records(
                $this->reportRepository->fetchGodownProductsStock($request)
            );
    }

    /**
     * Lot wise total stock
     *
     * @param  mixed $request
     * @return \Illuminate\Http\Response
     */
    public function lotStock(Request $request)
    {
        return $this->responseService->records(
            $this->reportRepository->fetchLotStock($request)
        );
    }

    /**
     * Lot wise products stock
     *
     * @param  mixed $request
     * @return \Illuminate\Http\Response
     */
    public function productsLotStock(Request $request)
    {
        return $this->responseService->records(
            $this->reportRepository->fetchProductsLotStock($request)
        );
    }

    /**
     * Transfer wise product movement
     *
     * @param  mixed $request
     * @return \Illuminate\Http\Response
     */
    public function productMovement(Request $request)
    {
        return $this->responseService->records(
            $this->reportRepository->fetchProductMovement($request)
        );
    }

    /**
     * Transfer wise account / godown movement
     *
     * @param  mixed $request
     * @return \Illuminate\Http\Response
     */
    public function godownMovement(Request $request)
    {
        return $this->responseService->records(
            $this->reportRepository->fetchGodownMovement($request)
        );
    }

    /**
     * Transfers via associated agents
     *
     * @param  mixed $request
     * @return \Illuminate\Http\Response
     */
    public function agentTrasnfers(Request $request)
    {
        return $this->responseService->records(
            $this->reportRepository->fetchAgentTransfers($request)
        );
    }

    /**
     * Purchase, sales and inter godown transfers
     *
     * @param  mixed $request
     * @return \Illuminate\Http\Response
     */
    public function allTransfers(Request $request)
    {
        return $this->responseService->records(
            $this->reportRepository->fetchAllTransfers($request)
        );
    }
}
