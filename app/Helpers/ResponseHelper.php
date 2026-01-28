<?php

if (!function_exists('apiSuccess')) {
    /**
     * Return a successful API response
     */
    function apiSuccess($data = null, $message = 'Success', $statusCode = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }
}

if (!function_exists('apiCreated')) {
    /**
     * Return a created (201) API response
     */
    function apiCreated($data = null, $message = 'Resource created successfully')
    {
        return apiSuccess($data, $message, 201);
    }
}

if (!function_exists('apiError')) {
    /**
     * Return an error API response
     */
    function apiError($message = 'Error', $statusCode = 400, $data = null)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }
}

if (!function_exists('apiNotFound')) {
    /**
     * Return a not found (404) API response
     */
    function apiNotFound($message = 'Resource not found')
    {
        return apiError($message, 404);
    }
}

if (!function_exists('apiUnauthorized')) {
    /**
     * Return an unauthorized (401) API response
     */
    function apiUnauthorized($message = 'Unauthorized')
    {
        return apiError($message, 401);
    }
}

if (!function_exists('apiForbidden')) {
    /**
     * Return a forbidden (403) API response
     */
    function apiForbidden($message = 'Forbidden')
    {
        return apiError($message, 403);
    }
}

if (!function_exists('apiValidationError')) {
    /**
     * Return a validation error (422) API response
     */
    function apiValidationError($errors, $message = 'Validation failed')
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], 422);
    }
}
