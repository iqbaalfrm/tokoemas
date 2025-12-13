<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use League\Flysystem\UnableToRetrieveMetadata;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (UnableToRetrieveMetadata $e, $request) {
            // Handle the specific case where Livewire temporary files are not found
            if (str_contains($e->getFile(), 'livewire-tmp')) {
                // Log the error but don't throw it, just return a response
                \Log::warning('Livewire temporary file not found: ' . $e->getFile());

                if ($request->is('api/*') || $request->wantsJson()) {
                    return response()->json([
                        'message' => 'File temporary tidak ditemukan, silakan coba unggah kembali.',
                        'error' => 'File upload temporary tidak ditemukan'
                    ], 422);
                }

                // For web requests, redirect back with an error
                return back()->withErrors(['upload' => 'File temporary tidak ditemukan, silakan coba unggah kembali.']);
            }

            return null; // Let other handlers deal with it if it's not a livewire-tmp issue
        });
    })->create();
