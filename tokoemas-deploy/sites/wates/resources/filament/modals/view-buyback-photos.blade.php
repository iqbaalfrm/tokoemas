<div class="p-2">
    @if($items->isEmpty())
        <p class="text-gray-500 dark:text-gray-400">Tidak ada foto yang di-upload untuk buyback ini.</p>
    @else
        <div class="space-y-4 max-h-[70vh] overflow-y-auto p-4">
            @foreach($items as $item)
                <div class="rounded-lg border border-gray-300 dark:border-gray-600 p-4 text-center"> {{-- Tambahkan text-center di sini --}}
                    <p class="mb-2 font-semibold text-gray-800 dark:text-gray-200">{{ $item->nama_produk }} ({{ $item->berat }}g)</p>
                    @if($item->foto)
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($item->foto) }}" 
                             alt="{{ $item->nama_produk }}" 
                             class="max-w-full h-auto rounded-md object-contain mb-2 mx-auto" {{-- Tambahkan mx-auto di sini --}}
                             style="max-height: 400px;">
                        <a href="{{ \Illuminate\Support\Facades\Storage::url($item->foto) }}" 
                           download="{{ \Illuminate\Support\Str::slug($item->nama_produk) }}.jpg"
                           class="inline-flex items-center gap-1 px-3 py-2 text-sm font-medium text-primary-600 hover:text-primary-500 bg-primary-50 dark:bg-gray-700 dark:text-primary-400 dark:hover:text-primary-300 rounded-lg transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                            </svg>
                            Download Foto
                        </a>
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400">Tidak ada foto untuk item ini.</p>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>