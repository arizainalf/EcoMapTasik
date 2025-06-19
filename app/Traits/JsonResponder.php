<?php
namespace App\Traits;

use Illuminate\Http\Response;

trait JsonResponder
{
public function successResponse($data = null, string $message = 'Success', int $code = Response::HTTP_OK, $meta = null)
    {
        return response()->json([
            'success' => true,
            'code'    => $code,
            'message' => $message,
            'data'    => $data,
            'meta'    => $meta,
        ], $code)->header('Content-Type', 'application/json');
    }

    public function errorResponse($data = null, $message = 'Error', $code = Response::HTTP_BAD_REQUEST)
    {
        return response()->json(compact('code', 'message', 'data'), $code)->header('Content-Type', 'application/json');
    }
}
