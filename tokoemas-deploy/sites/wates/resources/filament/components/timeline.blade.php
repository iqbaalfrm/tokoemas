@props(['logs'])

<div class="space-y-6">
    @forelse($logs as $index => $log)
        <div class="relative pl-8">
            @if(!$loop->last)
                <!-- Vertical line connecting items (excluding the last item) -->
                <div class="absolute left-4 top-8 w-0.5 bg-gray-200 h-full -translate-x-1/2"></div>
            @endif

            <!-- Timeline dot -->
            <div class="absolute left-0 top-8 w-8 h-8 rounded-full bg-blue-500 border-4 border-white flex items-center justify-center z-10">
                <svg class="w-2 h-2 text-white" fill="currentColor" viewBox="0 0 8 8">
                    <circle cx="4" cy="4" r="3" />
                </svg>
            </div>

            <!-- Timeline content -->
            <div class="ml-12 bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2 mb-2">
                    <div class="font-semibold text-gray-900">
                        {{ $log->user?->name ?? 'System' }}
                    </div>
                    <div class="text-sm text-gray-500">
                        {{ $log->created_at?->format('d M Y H:i') }}
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0">
                        @php
                            $actionColor = match(strtolower($log->action))
                            {
                                'created', 'dibuat' => 'bg-green-100 text-green-800',
                                'updated', 'status berubah', 'disetujui', 'disetujui perubahan' => 'bg-blue-100 text-blue-800',
                                'rejected', 'ditolak' => 'bg-red-100 text-red-800',
                                'pending', 'menunggu' => 'bg-yellow-100 text-yellow-800',
                                default => 'bg-gray-100 text-gray-800'
                            };
                        @endphp

                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $actionColor }}">
                            {{ $log->action }}
                        </span>
                    </div>

                    <div class="text-sm text-gray-600 flex-grow">
                        {{ $log->description }}
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="text-center py-8">
            <div class="flex justify-center mb-4">
                <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-gray-500">Belum ada riwayat proses.</p>
        </div>
    @endforelse
</div>