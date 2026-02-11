<?php

namespace App\Helpers;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Format response.
 */
class ResponseHelper
{
    /**
     * API Response
     *
     * @var array
     */
    public static array $response = [
        'meta' => [
            'code' => null,
            'status' => 'success',
            'message' => null,
            'pagination' => null
        ],
        'data' => null,
    ];

    /**
     * Give success response.
     * @param mixed|null $data
     * @param mixed|null $message
     * @param int $code
     * @param bool $pagination
     * @return JsonResponse
     */
    public static function success(mixed $data = null, string $message = null, int $code = Response::HTTP_OK, bool $pagination = false): JsonResponse
    {
        self::$response['meta']['message'] = $message;
        self::$response['meta']['code'] = $code;
        if ($pagination) {
            $dataArray = $data->toArray(request());
            self::$response['data'] = $dataArray['data'];
            self::$response['meta']['pagination'] = $dataArray['paginate'] ?? [];
        } else {
            self::$response['data'] = $data;
            unset(self::$response['meta']['pagination']);
        }

        return response()->json(self::$response, self::$response['meta']['code']);
    }

    /**
     * Give error response.
     * @param mixed|null $data
     * @param mixed|null $message
     * @param int $code
     * @return JsonResponse
     */
    public static function error(mixed $data = null, mixed $message = null, int $code = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        self::$response['meta']['status'] = 'error';
        self::$response['meta']['code'] = $code;
        self::$response['meta']['message'] = $message;
        self::$response['data'] = $data;
        unset(self::$response['meta']['pagination']);

        $response = response()->json(self::$response, self::$response['meta']['code']);

        throw new HttpResponseException($response);
    }
}
