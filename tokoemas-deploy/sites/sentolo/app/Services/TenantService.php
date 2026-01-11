<?php

namespace App\Services;

use Illuminate\Support\Facades\Config;

class TenantService
{
    /**
     * Current store configuration.
     */
    protected ?array $currentStore = null;

    /**
     * Current store code.
     */
    protected ?string $currentStoreCode = null;

    /**
     * Resolve store code dari host/domain.
     */
    public function resolveStoreFromHost(string $host): ?string
    {
        $domains = config('tenants.domains', []);
        
        // Exact match
        if (isset($domains[$host])) {
            return $domains[$host];
        }

        // Cek tanpa port
        $hostWithoutPort = explode(':', $host)[0];
        if (isset($domains[$hostWithoutPort])) {
            return $domains[$hostWithoutPort];
        }

        return null;
    }

    /**
     * Set current store context.
     */
    public function setCurrentStore(string $storeCode): void
    {
        $stores = config('tenants.stores', []);
        
        if (!isset($stores[$storeCode])) {
            throw new \InvalidArgumentException("Store '{$storeCode}' tidak ditemukan dalam konfigurasi.");
        }

        $this->currentStoreCode = $storeCode;
        $this->currentStore = array_merge($stores[$storeCode], ['code' => $storeCode]);

        // Bind ke container
        app()->instance('currentStore', (object) $this->currentStore);
        app()->instance('currentStoreCode', $storeCode);
    }

    /**
     * Get current store code.
     */
    public function getCurrentStoreCode(): ?string
    {
        return $this->currentStoreCode;
    }

    /**
     * Get current store configuration.
     */
    public function getCurrentStore(): ?object
    {
        return $this->currentStore ? (object) $this->currentStore : null;
    }

    /**
     * Get database connection untuk current store.
     */
    public function getStoreConnection(): string
    {
        return $this->currentStore['db_connection'] ?? config('database.default');
    }

    /**
     * Get member database connection (shared).
     */
    public function getMemberConnection(): string
    {
        return config('tenants.member_connection', 'member');
    }

    /**
     * Get storage path untuk current store.
     */
    public function getStorePath(string $subPath = ''): string
    {
        $basePath = storage_path('app/public/' . $this->currentStoreCode);
        
        if (!empty($subPath)) {
            $basePath .= '/' . ltrim($subPath, '/');
        }

        return $basePath;
    }

    /**
     * Get storage URL untuk current store.
     */
    public function getStoreUrl(string $subPath = ''): string
    {
        $baseUrl = '/storage/' . $this->currentStoreCode;
        
        if (!empty($subPath)) {
            $baseUrl .= '/' . ltrim($subPath, '/');
        }

        return $baseUrl;
    }

    /**
     * Cek apakah store code valid.
     */
    public function isValidStore(string $storeCode): bool
    {
        return isset(config('tenants.stores', [])[$storeCode]);
    }

    /**
     * Get semua store codes.
     */
    public function getAllStoreCodes(): array
    {
        return array_keys(config('tenants.stores', []));
    }

    /**
     * Get stores dalam group yang sama.
     */
    public function getStoresInSameGroup(?string $storeCode = null): array
    {
        $storeCode = $storeCode ?? $this->currentStoreCode;
        $stores = config('tenants.stores', []);
        
        if (!isset($stores[$storeCode])) {
            return [];
        }

        $group = $stores[$storeCode]['group'];
        
        return array_filter($stores, fn($store) => $store['group'] === $group);
    }
}
