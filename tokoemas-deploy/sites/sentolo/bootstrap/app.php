<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use League\Flysystem\UnableToRetrieveMetadata;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Register TenantResolver middleware untuk semua web routes
        $middleware->web(prepend: [
            \App\Http\Middleware\TenantResolver::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Handler untuk Livewire temp files
        $exceptions->render(function (UnableToRetrieveMetadata $e, $request) {
            if (str_contains($e->getFile(), 'livewire-tmp')) {
                \Log::warning('Livewire temporary file not found: ' . $e->getFile());

                if ($request->is('api/*') || $request->wantsJson()) {
                    return response()->json([
                        'message' => 'File temporary tidak ditemukan, silakan coba unggah kembali.',
                        'error' => 'File upload temporary tidak ditemukan'
                    ], 422);
                }

                return back()->withErrors(['upload' => 'File temporary tidak ditemukan, silakan coba unggah kembali.']);
            }

            return null;
        });

        // Custom error logging dengan store context
        $exceptions->report(function (Throwable $e) {
            // Skip jika masih dalam proses bootstrap
            if (!app()->bound('log')) {
                return;
            }
            
            // Generate error ID
            $errorId = strtoupper(substr(md5(time() . rand()), 0, 8));
            
            // Get store code
            $storeCode = env('STORE_CODE', 'unknown');
            
            // Get user id safely (hanya jika auth sudah tersedia)
            $userId = null;
            try {
                if (app()->bound('auth') && auth()->check()) {
                    $userId = auth()->id();
                }
            } catch (\Throwable $authError) {
                // Ignore auth errors during bootstrap
            }
            
            // Get URL safely
            $url = '';
            try {
                $url = request()->fullUrl();
            } catch (\Throwable $urlError) {
                $url = 'N/A';
            }
            
            // Log dengan context tambahan
            \Log::error("[{$storeCode}] Error ID: {$errorId}", [
                'error_id' => $errorId,
                'store_code' => $storeCode,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'url' => $url,
                'user_id' => $userId,
                'trace' => $e->getTraceAsString(),
            ]);
        })->stop();

        // Pastikan tidak menampilkan detail error di production
        $exceptions->shouldRenderJsonWhen(function (Request $request, Throwable $e) {
            return $request->expectsJson();
        });

    })->create();

