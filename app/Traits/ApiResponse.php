<?php

namespace App\Traits;

trait ApiResponse
{
    public function success($data = null, $message = 'Operation successful', $status = 200)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    public function error($message = 'Something went wrong', $error = null, $status = 500)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'error' => $error,
        ], $status);
    }
}
