<div class="relative overflow-visible" x-data="{ open: false }">
    <!-- Notification Bell -->
    <button
        @click="open = !open; $wire.toggleDropdown()"
        class="relative p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
        title="Notifikasi Approval"
    >
        <svg 
            class="w-6 h-6 text-gray-700 dark:text-gray-200" 
            xmlns="http://www.w3.org/2000/svg" 
            fill="none" 
            viewBox="0 0 24 24" 
            stroke="currentColor"
        >
            <path 
                stroke-linecap="round" 
                stroke-linejoin="round" 
                stroke-width="2" 
                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" 
            />
        </svg>
        
        @if(isset($unreadCount) && $unreadCount > 0)
            <span
                class="absolute bg-red-600 text-white text-xs rounded-full flex items-center justify-center font-bold pointer-events-none"
                style="right: -6px; top: -4px; z-index: 9999; width: 20px; height: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.5); background-color: #dc2626 !important;"
            >
                {{ min($unreadCount, 99) }}
            </span>
        @endif
    </button>

    <!-- Dropdown -->
    <div
        x-show="open"
        @click.away="open = false"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute mt-2 bg-white dark:bg-gray-800 rounded-md shadow-lg ring-1 ring-black ring-opacity-5 z-50"
        style="display: none; right: 0; width: 380px; min-width: 380px; max-width: 380px; box-sizing: border-box; overflow: hidden;"
    >
        <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center w-full">
            <h3 class="font-medium text-gray-900 dark:text-white">Notifikasi ({{ $unreadCount ?? 0 }})</h3>
            @if(isset($unreadCount) && $unreadCount > 0)
                <button
                    wire:click="markAllAsRead"
                    class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                >
                    Tandai semua sudah dibaca
                </button>
            @endif
        </div>
        
        <div class="max-h-96 overflow-y-auto w-full">
            @if(isset($notifications) && is_array($notifications) && count($notifications) > 0)
                <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($notifications ?? [] as $notification)
                        <li 
                            class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors"
                            wire:click="markAsRead('{{ $notification['id'] }}')"
                        >
                            <a 
                                href="{{ $notification['url'] }}" 
                                class="block"
                                @click.prevent="$wire.markAsRead('{{ $notification['id'] }}'); window.location.href = '{{ $notification['url'] }}';"
                            >
                                <div class="flex justify-between">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $notification['title'] }}
                                    </p>
                                </div>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-300">
                                    {{ $notification['message'] }}
                                </p>
                                <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">
                                    {{ $notification['created_at'] }}
                                </p>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="p-6 text-center" style="width: 100%; box-sizing: border-box; padding: 1.5rem;">
                    <svg 
                        class="mx-auto text-gray-400" 
                        style="width: 48px; height: 48px; max-width: 100%;"
                        xmlns="http://www.w3.org/2000/svg" 
                        fill="none" 
                        viewBox="0 0 24 24" 
                        stroke="currentColor"
                    >
                        <path 
                            stroke-linecap="round" 
                            stroke-linejoin="round" 
                            stroke-width="2" 
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" 
                        />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white px-2">Tidak ada notifikasi</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 px-4">
                        Semua notifikasi sudah dibaca.
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>