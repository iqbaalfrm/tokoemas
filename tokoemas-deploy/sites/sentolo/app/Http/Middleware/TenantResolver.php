<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\TenantService;

class TenantResolver
{
    /**
     * Handle an incoming request.
     *
     * Resolve store/tenant context berdasarkan:
     * 1. STORE_CODE dari .env (prioritas utama untuk multi-site deployment)
     * 2. Domain/Host dari request (fallback)
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tenantService = app(TenantService::class);
        
        // Prioritas 1: STORE_CODE dari .env
        $storeCode = env('STORE_CODE');
        
        // Prioritas 2: Resolve dari domain
        if (empty($storeCode)) {
            $host = $request->getHost();
            $storeCode = $tenantService->resolveStoreFromHost($host);
        }
        
        // Fallback ke default
        if (empty($storeCode)) {
            $storeCode = config('tenants.default', 'wates');
        }
        
        // Set tenant context
        $tenantService->setCurrentStore($storeCode);
        
        // Konfigurasi dinamis berdasarkan store
        $this->configureDynamicSettings($storeCode);
        
        return $next($request);
    }

    /**
     * Konfigurasi dinamis berdasarkan store.
     */
    protected function configureDynamicSettings(string $storeCode): void
    {
        // Cache prefix per store
        config([
            'cache.prefix' => $storeCode . '_cache_',
        ]);

        // Session cookie per store (untuk menghindari konflik)
        config([
            'session.cookie' => $storeCode . '_session',
        ]);

        // Logging channel dengan context store
        config([
            'logging.channels.single.path' => storage_path('logs/' . $storeCode . '-laravel.log'),
            'logging.channels.daily.path' => storage_path('logs/' . $storeCode . '-laravel.log'),
        ]);
    }
}
