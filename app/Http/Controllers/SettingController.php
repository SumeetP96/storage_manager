<?php

namespace App\Http\Controllers;

use App\Setting;
use Illuminate\Http\Request;
use App\Services\Response\ResponseService;

class SettingController extends Controller
{
    /**
     * Response service
     *
     * @var \App\Services\Response\ResponseService
     */
    private $responseService;

    public function __construct()
    {
        $this->responseService = new ResponseService;
    }

    /**
     * Get transaction lock date
     *
     * @return void
     */
    public function getLockDate()
    {
        return Setting::where('id', 1)
            ->selectRaw('
                transaction_lock_date as lockDate,
                DATE_FORMAT(transaction_lock_date, "%d-%m-%Y") as lockDateRaw
            ')
            ->first();
    }

    /**
     * Update transaction lock date
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        Setting::find(1)->update([
            'transaction_lock_date' => $request->lockDate
        ]);

        return $this->responseService->success();
    }
}
