<div class="space-y-4 max-h-96 overflow-y-auto p-2">
    @if($logs->isEmpty())
        <div class="text-center py-4 text-gray-500">
            <p>Tidak ada riwayat untuk ditampilkan.</p>
        </div>
    @else
        <div class="relative">
            <!-- Vertical line -->
            <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-200 dark:bg-gray-700"></div>
            
            @foreach($logs as $log)
                <div class="relative pl-12 pb-8">
                    <!-- Timeline dot -->
                    <div class="absolute left-0 top-1 w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center z-10">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="flex items-start justify-between">
                            <div>
                                <h4 class="font-bold text-gray-900 dark:text-white">
                                    {{ $log->user ? $log->user->name : 'System' }}
                                </h4>
                                <p class="italic text-blue-600 dark:text-blue-400 mt-1">
                                    {{ $log->action }}
                                </p>
                                @if($log->description)
                                    <p class="text-gray-600 dark:text-gray-300 mt-2">
                                        {{ $log->description }}
                                    </p>
                                @endif
                            </div>
                            
                            <div class="text-right text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                <div>{{ $log->created_at->format('d M Y') }}</div>
                                <div class="font-medium">{{ $log->created_at->format('H:i') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>