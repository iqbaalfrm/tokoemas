<?php

namespace App\Models\Concerns;

use App\Services\TenantService;

/**
 * Trait BelongsToTenant
 *
 * Gunakan trait ini di Model yang datanya harus diakses
 * dari database sesuai tenant/store yang aktif.
 */
trait BelongsToTenant
{
    /**
     * Get the database connection untuk model ini.
     * Akan otomatis menggunakan connection sesuai current store.
     */
    public function getConnectionName(): ?string
    {
        // Jika sudah di-set secara eksplisit, gunakan itu
        if ($this->connection) {
            return $this->connection;
        }

        // Resolve dari TenantService
        $tenantService = app(TenantService::class);
        
        return $tenantService->getStoreConnection();
    }

    /**
     * Boot trait - tambahkan store_code scope otomatis jika ada kolom.
     */
    public static function bootBelongsToTenant(): void
    {
        // Auto-set store_code saat creating jika model punya kolom store_code
        static::creating(function ($model) {
            if ($model->hasStoreCodeColumn() && empty($model->store_code)) {
                $storeCode = app('currentStoreCode') ?? config('tenants.default');
                $model->store_code = $storeCode;
            }
        });
    }

    /**
     * Cek apakah model punya kolom store_code.
     */
    public function hasStoreCodeColumn(): bool
    {
        return in_array('store_code', $this->getFillable()) || 
               $this->getConnection()->getSchemaBuilder()->hasColumn($this->getTable(), 'store_code');
    }

    /**
     * Scope: filter by current store.
     */
    public function scopeCurrentStore($query)
    {
        $storeCode = app('currentStoreCode') ?? config('tenants.default');
        
        return $query->where('store_code', $storeCode);
    }

    /**
     * Scope: filter by store group (untuk query lintas store dalam 1 group).
     */
    public function scopeCurrentGroup($query)
    {
        $tenantService = app(TenantService::class);
        $storesInGroup = $tenantService->getStoresInSameGroup();
        $storeCodes = array_keys($storesInGroup);
        
        return $query->whereIn('store_code', $storeCodes);
    }
}
