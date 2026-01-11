<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller untuk serve file storage tanpa symlink.
 * Diperlukan karena hosting tidak support SSH untuk membuat symlink.
 */
class StorageController extends Controller
{
    /**
     * Serve file dari storage.
     * Route: /storage/{path}
     */
    public function serve(Request $request, string $path): Response
    {
        // Path lengkap di storage
        $fullPath = storage_path('app/public/' . $path);

        // Cek file exists
        if (!file_exists($fullPath)) {
            abort(404, 'File tidak ditemukan');
        }

        // Cek apakah file (bukan direktori)
        if (!is_file($fullPath)) {
            abort(404, 'Bukan file');
        }

        // Get mime type
        $mimeType = mime_content_type($fullPath);

        // Return file response dengan cache
        return response()->file($fullPath, [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'public, max-age=31536000',
        ]);
    }

    /**
     * Serve file dari store tertentu.
     * Route: /storage/{store}/{path}
     */
    public function serveStore(Request $request, string $store, string $path): Response
    {
        // Validasi store code
        $validStores = ['wates', 'wates1', 'sentolo', 'sentolo1'];
        if (!in_array($store, $validStores)) {
            abort(404, 'Store tidak valid');
        }

        // Path lengkap di storage
        $fullPath = storage_path('app/public/' . $store . '/' . $path);

        // Cek file exists
        if (!file_exists($fullPath)) {
            abort(404, 'File tidak ditemukan');
        }

        // Cek apakah file (bukan direktori)
        if (!is_file($fullPath)) {
            abort(404, 'Bukan file');
        }

        // Get mime type
        $mimeType = mime_content_type($fullPath);

        // Return file response dengan cache
        return response()->file($fullPath, [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'public, max-age=31536000',
        ]);
    }
}
