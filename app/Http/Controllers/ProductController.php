<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\ProductRepository;
use App\Services\ProductService;
use App\Services\Response\ResponseService;

class ProductController extends Controller
{
    /**
     * @var \App\Services\Response\ResponseService
     */
    protected $responseService;

    /**
     * @var \App\Repositories\ProductRepository
     */
    protected $productRepository;

    /**
     * @var \App\Services\ProductService
     */
    protected $productService;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->responseService = new ResponseService;
        $this->productRepository = new ProductRepository;
        $this->productService = new ProductService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $this->responseService
            ->records($this->productRepository->fetchAll($request));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($duplicate = $this->productService->findDuplicateProductLot($request)) {
            return $this->responseService->error($duplicate);
        }

        $this->productService->validateRequest($request);

        return $this->responseService
            ->success(
                $this->productRepository->create($request)
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
            ->record($this->productRepository->fetchOne($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if ($duplicate = $this->productService->findDuplicateProductLot($request, $id)) {
            return $this->responseService->error($duplicate);
        }

        $this->productService->validateRequest($request);

        $this->productRepository->update($request, $id);

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
        $this->productRepository->destroy($id);

        return $this->responseService->success();
    }

    /**
     * Fetch records for autocomplete
     */
    public function autocomplete()
    {
        return $this->responseService
            ->autocomplete($this->productRepository->fetchAutocompletes());
    }

    /**
     * Fetch records for autocomplete with stock
     */
    public function autocompleteWithStock($id)
    {
        return $this->responseService
            ->autocomplete(
                $this->productRepository->fetchAutocompletesWithStock($id)
            );
    }

    /**
     * Fetch selected record details
     */
    public function details($id, $godownId = null)
    {
        if ($godownId == 'undefined') $godownId = NULL;
        return $this->productRepository->details($id, $godownId);
    }
}
