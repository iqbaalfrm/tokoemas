<?php

namespace App\Models\Concerns;

use App\Services\TenantService;

/**
 * Trait BelongsToMemberDatabase
 *
 * Gunakan trait ini di Model yang datanya disimpan di
 * database member (shared across all stores).
 */
trait BelongsToMemberDatabase
{
    /**
     * Get the database connection untuk model ini.
     * Selalu menggunakan member connection.
     */
    public function getConnectionName(): ?string
    {
        // Jika sudah di-set secara eksplisit, gunakan itu
        if ($this->connection) {
            return $this->connection;
        }

        // Gunakan member connection
        return config('tenants.member_connection', 'member');
    }
}
