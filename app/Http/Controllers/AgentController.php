<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\AgentRepository;
use App\Http\Requests\UpdateAgentRequest;
use App\Http\Requests\CreateAgentRequest;
use App\Services\Response\ResponseService;

class AgentController extends Controller
{
    /**
     * @var \App\Services\Response\ResponseService
     */
    protected $responseService;

    /**
     * @var \App\Repositories\AgentRepository
     */
    protected $agentRepository;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->agentRepository = new AgentRepository;
        $this->responseService = new ResponseService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $this->responseService
            ->records($this->agentRepository->fetchAll($request));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateAgentRequest $request)
    {
        return $this->responseService
            ->success(
                $this->agentRepository->create($request)
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
            ->record($this->agentRepository->fetchOne($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAgentRequest $request, $id)
    {
        $this->agentRepository->update($request, $id);

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
        $this->agentRepository->destroy($id);

        return $this->responseService->success();
    }

    /**
     * Fetch records for autocomplete
     */
    public function autocomplete()
    {
        return $this->responseService
            ->autocomplete($this->agentRepository->fetchAutocompletes());
    }

    /**
     * Fetch records for autocomplete
     */
    public function autocompleteWithTransfer()
    {
        return $this->responseService
            ->autocomplete($this->agentRepository->fetchAutocompletesWithTransfer());
    }
}
