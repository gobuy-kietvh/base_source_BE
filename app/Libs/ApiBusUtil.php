<?php

namespace App\Libs;

use App\Enums\{
    ApiCodeNo,
    ApiStatusCode,
};
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
class ApiBusUtil
{
    /**
     * API success response
     *
     * @param array|null $data
     * @param array $headers
     * @return \Illuminate\Http\JsonResponse
     */
    public static function successResponse($message = null, $data = null, $statusCode = 200)
    {
        if (empty($message)) {
            $message = 'Data has been successfully retrieved.';
        }
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $statusCode);
    }

    /**
     * API error response
     *
     * @param ApiCodeNo|string|null $codeNo
     * @param ApiStatusCode|int $status
     * @param string|null $message
     * @return \Illuminate\Http\JsonResponse
     */
    public static function errorResponse($message = null, $statusCode = 500)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $statusCode);
    }

    /**
     * Pre-build error response
     *
     * @param ApiCodeNo $codeNo
     * @return \Illuminate\Http\JsonResponse
     */
    public static function preBuiltErrorResponse(ApiCodeNo $codeNo, $message)
    {
        switch ($codeNo) {
            case ApiCodeNo::REQUIRED_PARAMETER:
            case ApiCodeNo::VALIDATE_PARAMETER:
                return self::errorResponse(
                    $message,
                    ResponseAlias::HTTP_BAD_REQUEST,
                );
            default:
                return self::errorResponse(
                    "Server errors",
                    ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,
                );
        }
    }
}
