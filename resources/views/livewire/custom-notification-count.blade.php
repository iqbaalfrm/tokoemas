@php
    $unreadCount = $this->unreadCount ?? 0;
    $hasUnread = $unreadCount > 0;
@endphp

<div class="relative">
    <a
        href="{{ route('filament.admin.pages.daftar-approval') }}"
        class="relative p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
        title="Notifikasi Approval"
        wire:ignore
    >
        <svg
            class="w-5 h-5 text-gray-700 dark:text-gray-200"
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

        @if($hasUnread)
            <span
                class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-bold"
            >
                {{ min($unreadCount, 99) }}
            </span>
        @endif
    </a>
</div>