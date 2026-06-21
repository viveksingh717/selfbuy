<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;

class ResponseService
{
    public $status;    
    public $message;
    public $data;   
    public $type;
    public $success;
    public $validation;
    public $code;

    final public const TYPE_SUCCESS = 'success';
    final public const TYPE_ERROR = 'error';
    final public const TYPE_VALIDATION = 'validation_error';
    final public const TYPE_NOT_FOUND = 'not_found';
    final public const TYPE_UNAUTHORIZED = 'unauthorized';
    final public const TYPE_FORBIDDEN = 'forbidden';
    final public const TYPE_SERVER_ERROR = 'server_error';
    final public const BAD_REQUEST = 'bad_request';
    final public const DOWNTIME = 'downtime';
    final public const REQUEST_CREATED = 'request_created';

    public function __construct()
    {
        $this->status = null;
        $this->message = '';
        $this->data = [];
        $this->type = null;
        $this->success = false;
        $this->validation = [];
        $this->code = null;
    }


    /**
     * Success Response
     */
    public function setSuccessResponse(string $message, $data): JsonResponse
    {
        return response()->json([
            'status' => 200,
            'type' => self::TYPE_SUCCESS,
            'success' => true,
            'message' => $message,
            'validation' => [],
            'data' => $data
        ], 200);
    }

    /**
     * Created Request Response (201)
     */
    public function setCreatedResponse(string $message, $data): JsonResponse
    {
        return response()->json([
            'status' => 201,
            'type' => self::REQUEST_CREATED,
            'success' => true,
            'message' => $message,
            'validation' => [],
            'data' => $data
        ], 201);
    }

    /**
     * Validation Error (422)
     */
    public function setValidationResponse($errors): JsonResponse
    {
        $finalError = [];

        foreach ($errors->messages() as $field => $messages) {
            $finalError[$field] = $messages[0];
        }

        return response()->json([
            'status' => 422,
            'type' => self::TYPE_VALIDATION,
            'success' => false,
            'message' => 'Validation error',
            'validation' => $finalError,
            'data' => []
        ], 422);
    }

    /**
     * Not Found (404)
     */
    public function setNotFoundResponse(string $message = 'Resource Not Found'): JsonResponse
    {
        return response()->json([
            'status' => 404,
            'type' => self::TYPE_NOT_FOUND,
            'success' => false,
            'message' => $message
        ], 404);
    }

    /**
     * Error (500)
     */
    public function setErrorResponse($message = 'Something went wrong', $data = [])
    {
        return response()->json([
            'status' => false,
            'type' => self::TYPE_ERROR,
            'success' => false,
            'message' => $message,
            'data' => $data
        ], 500);
    }

    /**
     * Unauthorized (401)
     */
    public function setUnauthorizedResponse(string $message = 'Unauthorized Access'): JsonResponse
    {
        return response()->json([
            'status' => 401,
            'type' => self::TYPE_UNAUTHORIZED,
            'success' => false,
            'message' => $message
        ], 401);
    }

    /**
     * Forbidden (403)
     */
    public function setForbiddenResponse(string $message = 'Forbidden'): JsonResponse
    {
        return response()->json([
            'status' => 403,
            'type' => self::TYPE_FORBIDDEN,
            'success' => false,
            'message' => $message,
            'validation' => [],
            'data' => []
        ], 403);
    }

    /**
     * Internal Server Error (500)
     */
    public function serverError(string $message = 'Internal Server Error'): JsonResponse
    {
        return response()->json([
            'status' => 500,
            'type' => self::TYPE_SERVER_ERROR,
            'success' => false,
            'message' => $message,
            'validation' => [],
            'data' => []
        ], 500);
    }

    /**
     * Custom Response
     */
    public function setCustomResponse($status, string $message, $data, int $code, $type, $success): JsonResponse
    {
        return response()->json([
            'status' => $code,
            'type' => $type,
            'success' => $success,
            'message' => $message,
            'validation' => [],
            'data' => $data
        ], $code);
    }
}