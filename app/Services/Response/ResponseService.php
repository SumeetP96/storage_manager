<?php

namespace App\Services\Response;

class ResponseService
{
    public function autocomplete($records)
    {
        return response()->json([
            'records'   => $records
        ], 200);
    }

    public function record($record)
    {
        return response()->json([
            'record'   => $record
        ], 200);
    }

    public function records($data)
    {
        return response()->json([
            'records'   => $data['records'],
            'total'     => $data['total']
        ], 200);
    }

    public function success($id = null, $message = null)
    {
        return response()->json([
            'success'   => TRUE,
            'id'        => $id,
            'message'   => $message
        ], 200);
    }

    public function failure($message = '')
    {
        return response()->json([
            'success'   => FALSE,
            'message'   => $message
        ], 200);
    }

    public function warning($message = '')
    {
        return response()->json([
            'warning'   => TRUE,
            'message'   => $message
        ], 200);
    }

    public function error($errorMessages = [])
    {
        return response()->json([
            'errors'    => $errorMessages
        ], 422);
    }
}
