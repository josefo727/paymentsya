<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\JsonResponse;

class ResponseMacroServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        /**
         * Create a new JSON response for successful operations.
         *
         * @param  mixed  $response
         * @param  string  $message
         * @param  int  $status
         * @return \Illuminate\Http\JsonResponse
         */
        Response::macro('success', function ($response, string $message = 'Data successfully obtained', int $status = 200): JsonResponse {
            return new JsonResponse([
                'data' => $response,
                'message' => $message,
                'status' => $status,
            ], $status);
        });

        /**
         * Create a new JSON response for error operations.
         *
         * @param  string  $message
         * @param  int  $status
         * @return \Illuminate\Http\JsonResponse
         */
        Response::macro('error', function (string $message = 'An error occurred', int $status = 400): JsonResponse {
            return new JsonResponse([
                'error' => [
                    'message' => $message,
                    'status' => $status,
                ],
            ], $status);
        });
    }
}
