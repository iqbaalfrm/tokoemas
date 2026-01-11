<?php

namespace App\Helpers;

use App\Services\TenantService;
use Illuminate\Support\Facades\Storage;

/**
 * Helper class untuk mengelola storage path per tenant/store.
 */
class TenantStorage
{
    /**
     * Get storage path untuk current store.
     *
     * @param string $subPath Sub-path dalam folder store
     * @return string Full storage path
     */
    public static function path(string $subPath = ''): string
    {
        $storeCode = app('currentStoreCode') ?? config('tenants.default', 'wates');
        $basePath = storage_path('app/public/' . $storeCode);

        if (!empty($subPath)) {
            $basePath .= '/' . ltrim($subPath, '/');
        }

        return $basePath;
    }

    /**
     * Get storage URL untuk current store.
     *
     * @param string $subPath Sub-path dalam folder store
     * @return string URL ke file
     */
    public static function url(string $subPath = ''): string
    {
        $storeCode = app('currentStoreCode') ?? config('tenants.default', 'wates');
        $baseUrl = '/storage/' . $storeCode;

        if (!empty($subPath)) {
            $baseUrl .= '/' . ltrim($subPath, '/');
        }

        return $baseUrl;
    }

    /**
     * Store file ke storage milik current store.
     *
     * @param mixed $file File yang akan disimpan (UploadedFile)
     * @param string $directory Direktori tujuan (contoh: 'products', 'receipts')
     * @param string|null $filename Nama file (opsional, jika null akan generate)
     * @return string|false Path file yang disimpan, atau false jika gagal
     */
    public static function store($file, string $directory, ?string $filename = null): string|false
    {
        $storeCode = app('currentStoreCode') ?? config('tenants.default', 'wates');
        $fullDirectory = $storeCode . '/' . ltrim($directory, '/');

        if ($filename) {
            return Storage::disk('public')->putFileAs($fullDirectory, $file, $filename);
        }

        return Storage::disk('public')->putFile($fullDirectory, $file);
    }

    /**
     * Delete file dari storage milik current store.
     *
     * @param string $path Path file relatif dari folder store
     * @return bool
     */
    public static function delete(string $path): bool
    {
        $storeCode = app('currentStoreCode') ?? config('tenants.default', 'wates');
        $fullPath = $storeCode . '/' . ltrim($path, '/');

        return Storage::disk('public')->delete($fullPath);
    }

    /**
     * Cek apakah file exists di storage milik current store.
     *
     * @param string $path Path file relatif dari folder store
     * @return bool
     */
    public static function exists(string $path): bool
    {
        $storeCode = app('currentStoreCode') ?? config('tenants.default', 'wates');
        $fullPath = $storeCode . '/' . ltrim($path, '/');

        return Storage::disk('public')->exists($fullPath);
    }

    /**
     * Ensure direktori store sudah ada.
     *
     * @param array $directories Daftar direktori yang harus dibuat
     * @return void
     */
    public static function ensureDirectories(array $directories = ['products', 'receipts', 'ktp']): void
    {
        $storeCode = app('currentStoreCode') ?? config('tenants.default', 'wates');

        foreach ($directories as $dir) {
            $fullPath = $storeCode . '/' . $dir;
            if (!Storage::disk('public')->exists($fullPath)) {
                Storage::disk('public')->makeDirectory($fullPath);
            }
        }
    }
}
