<?php

namespace App\Providers;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Load helper functions
        require_once app_path('Helpers/ResponseHelper.php');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register Response macros
        Response::macro('success', function ($data = null, $message = 'Success', $statusCode = 200) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $data,
            ], $statusCode);
        });

        Response::macro('created', function ($data = null, $message = 'Resource created successfully') {
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $data,
            ], 201);
        });

        Response::macro('error', function ($message = 'Error', $statusCode = 400, $data = null) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'data' => $data,
            ], $statusCode);
        });

        Response::macro('notFound', function ($message = 'Resource not found') {
            return response()->json([
                'success' => false,
                'message' => $message,
            ], 404);
        });

        Response::macro('unauthorized', function ($message = 'Unauthorized') {
            return response()->json([
                'success' => false,
                'message' => $message,
            ], 401);
        });

        Response::macro('forbidden', function ($message = 'Forbidden') {
            return response()->json([
                'success' => false,
                'message' => $message,
            ], 403);
        });

        Response::macro('validationError', function ($errors, $message = 'Validation failed') {
            return response()->json([
                'success' => false,
                'message' => $message,
                'errors' => $errors,
            ], 422);
        });
    }
}

