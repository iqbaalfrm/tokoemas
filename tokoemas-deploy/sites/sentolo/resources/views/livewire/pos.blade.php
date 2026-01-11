<div class="min-h-screen p-2 lg:p-6" style="font-family: 'Poppins'" x-data="{ showMobileCart: false }">
    <div class="max-w-[1600px] mx-auto">
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-4 lg:gap-6">
            <div class="xl:col-span-2">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-4 lg:p-6 mb-4 lg:mb-6">
                    <div class="grid grid-cols-1 gap-3 lg:gap-4">
                        <div class="relative">
                            <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <input wire:model.live.debounce.300ms='search' type="text"
                                placeholder="Cari nama atau SKU produk..."
                                class="w-full pl-12 pr-4 py-3 border-2 border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-700 dark:border-gray-600 text-gray-900 dark:text-white focus:border-green-500 focus:ring-2 focus:ring-green-200 dark:focus:ring-green-800 transition-all duration-200">
                        </div>

                        <div class="flex gap-2 lg:gap-3">
                            <div class="relative flex-1">
                                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z">
                                    </path>
                                </svg>
                                <input wire:model.live='barcode' type="text" placeholder="Scan barcode..." autofocus
                                    id="barcode"
                                    class="w-full pl-12 pr-4 py-3 border-2 border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-700 dark:border-gray-600 text-gray-900 dark:text-white focus:border-green-500 focus:ring-2 focus:ring-green-200 dark:focus:ring-green-800 transition-all duration-200">
                            </div>
                            <x-filament::button x-data="" x-on:click="$dispatch('toggle-scanner')"
                                class="px-3 lg:px-4 py-3 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                                <img src="{{ asset('images/qrcode-scan.svg') }}" class="w-6 h-6" alt="Scan QR Code"/>
                            </x-filament::button>
                        </div>
                    </div>
                    {{-- MODAL SCAN CAMERA --}}
                    <livewire:scanner-modal-component>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-4 lg:p-6 mb-4 lg:mb-6">
                    <div class="overflow-x-auto -mx-4 px-4 lg:mx-0 lg:px-0">
                        <div class="flex gap-2 lg:gap-3 pb-2 whitespace-nowrap">
                            @foreach ($categories as $item)
                                <button wire:click="setCategory({{ $item['id'] ?? null }})"
                                    class="category-btn px-4 lg:px-6 py-2 lg:py-3 rounded-xl font-medium text-sm lg:text-base transition-all duration-300 transform hover:scale-105
                                    {{ $selectedCategory === $item['id']
                                        ? 'bg-gradient-to-r from-green-500 to-green-600 text-white shadow-lg'
                                        : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-white' }}">
                                    {{ $item['name'] }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-4 lg:p-6 relative mb-20 xl:mb-0"> <div wire:loading wire:target="search, selectedCategory" class="absolute inset-0 bg-white/50 dark:bg-gray-800/50 backdrop-blur-sm flex items-center justify-center z-10 rounded-2xl">
                        <x-filament::loading-indicator class="h-10 w-10" />
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 2xl:grid-cols-5 gap-2 lg:gap-4">
                        @forelse ($products as $item)
                            <div wire:click="addToOrder({{ $item->id }})"
                                class="group bg-gray-50 dark:bg-gray-700 rounded-xl shadow-md hover:shadow-2xl transform hover:scale-105 transition-all duration-300 cursor-pointer overflow-hidden">
                                <div class="relative">
                                    <img src="{{ $item->image ? asset('storage/' . $item->image) : asset('images/default-product.png') }}" alt="{{ $item->name }}"
                                        class="w-full h-24 lg:h-32 object-cover">
                                </div>
                                <div class="p-2 lg:p-4">
                                    <h3 class="text-xs lg:text-sm font-semibold text-gray-800 dark:text-white line-clamp-2 mb-1 lg:mb-2">
                                        {{ $item->name }}</h3>
                                    <div class="flex items-center justify-between w-full">
                                        <p class="text-sm lg:text-lg font-bold text-green-600 dark:text-green-400">
                                            Rp {{ number_format($item->selling_price, 0, ',', '.') }}
                                        </p>
                                        <p class="text-xs lg:text-sm font-medium text-gray-600 dark:text-gray-400">
                                            ({{ $item->stock }})
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full text-center py-12 text-gray-500 dark:text-gray-400">
                                <svg class="w-16 h-16 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                                </svg>
                                <p class="text-lg font-semibold">Produk tidak ditemukan</p>
                                <p class="text-sm">Coba ubah kata kunci pencarian atau filter kategori Anda.</p>
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        {{ $products->links() }}
                    </div>
                </div>
                
            </div>

            <!-- CART SECTION - Hidden on mobile, shown on XL screens -->
            <div class="hidden xl:block xl:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 sticky top-6">
                    @include('livewire.partials.cart-content')
                </div>
            </div>
        </div>
    </div>

    <!-- FLOATING CART BUTTON - Mobile Only -->
    <div class="xl:hidden fixed bottom-4 right-4 z-40">
        <button @click="showMobileCart = true" 
            class="relative w-16 h-16 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-full shadow-2xl flex items-center justify-center transform hover:scale-110 transition-all duration-200">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                </path>
            </svg>
            @if (!empty($order_items))
                <span class="absolute -top-1 -right-1 w-6 h-6 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center">
                    {{ count($order_items) }}
                </span>
            @endif
        </button>
    </div>

    <!-- MOBILE CART MODAL -->
    <div x-show="showMobileCart" x-cloak
        class="xl:hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">
        <div class="absolute inset-x-0 bottom-0 max-h-[90vh] bg-white dark:bg-gray-800 rounded-t-3xl shadow-2xl overflow-hidden"
            x-show="showMobileCart"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="translate-y-full"
            x-transition:enter-end="translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="translate-y-0"
            x-transition:leave-end="translate-y-full">
            
            <!-- Handle bar -->
            <div class="flex justify-center pt-3 pb-2">
                <div class="w-12 h-1.5 bg-gray-300 dark:bg-gray-600 rounded-full"></div>
            </div>
            
            <!-- Close button -->
            <button @click="showMobileCart = false" class="absolute top-3 right-4 p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            
            <div class="p-4 overflow-y-auto max-h-[calc(90vh-60px)]">
                @include('livewire.partials.cart-content')
            </div>
        </div>
    </div>

    @if ($showCheckoutModal)
<div wire:ignore.self x-data="{
    init() {
        @this.calculateTotal();
        if (@this.payment_method_id) {
            @this.updatedPaymentMethodId(@this.payment_method_id);
        }
    }
}" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex justify-center items-end md:items-center z-50 p-0 md:p-4">
    <div class="bg-white dark:bg-gray-800 rounded-t-3xl md:rounded-2xl shadow-2xl w-full max-w-2xl max-h-[95vh] md:max-h-[90vh] flex flex-col transform animate-modal-appear">
        <!-- Header dengan close button untuk mobile -->
        <div class="shrink-0 bg-white dark:bg-gray-800 px-4 pt-4 pb-2 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center md:hidden">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white">Checkout</h3>
            <button type="button" wire:click="$set('showCheckoutModal', false)" class="p-2 text-gray-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <div class="flex-1 p-4 md:p-6 overflow-y-auto overscroll-contain" style="-webkit-overflow-scrolling: touch;">
            <form wire:submit="checkout">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- ================= KIRI ================= --}}
                    <div class="col-span-1">
                        {{-- Total --}}
                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-4 mb-6 text-white">
                            <div class="text-sm opacity-90">Total Belanja</div>
                            <div class="text-3xl font-bold">
                                Rp {{ number_format($total_price, 0, ',', '.') }}
                            </div>
                        </div>

                        {{-- Nomor HP --}}
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-white mb-2">
                                Nomor HP Customer
                            </label>
                            <input type="text" wire:model.live="no_hp"
                                class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                placeholder="Masukkan nomor HP" autocomplete="off">
                            <p class="text-xs text-gray-500 mt-1">Masukkan nomor HP untuk mendeteksi member otomatis.</p>
                        </div>

                        {{-- Nama Customer --}}
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-white mb-2">
                                Nama Customer
                            </label>
                            <input type="text" wire:model="name"
                                class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                placeholder="Masukkan nama customer">
                        </div>

                        {{-- Alamat Customer --}}
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-white mb-2">
                                Alamat
                            </label>
                            <textarea wire:model="alamat"
                                class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                rows="2" placeholder="Masukkan alamat customer"></textarea>
                        </div>

                        {{-- Metode Pembayaran --}}
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-white mb-2">
                                Metode Pembayaran
                            </label>
                            <select wire:model.live="payment_method_id"
                                class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                required>
                                <option value="">Pilih metode pembayaran</option>
                                @foreach ($payment_methods as $method)
                                    <option value="{{ $method->id }}">{{ $method->name }}</option>
                                @endforeach
                            </select>
                            @error('payment_method_id')
                                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- ================= KANAN ================= --}}
                    <div class="col-span-1">
                        {{-- Kembalian --}}
                        <div class="mb-6">
                            <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-xl p-4 mb-6 text-white">
                                <div class="text-sm opacity-90">Kembalian</div>
                                <div class="text-3xl font-bold transition-all duration-300">
                                    Rp {{ number_format($change, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>

                        {{-- Nominal Bayar --}}
                        @if ($is_cash)
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-white mb-2">
                                    Nominal Bayar
                                </label>
                                <div class="relative">
                                    <span
                                        class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400 font-medium">
                                        Rp
                                    </span>
                                    <input type="text" wire:model.live="cash_received"
                                        x-data="{
                                            formatCurrency(value) {
                                                return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.')
                                            }
                                        }"
                                        x-on:input="
                                            let value = $event.target.value.replace(/\./g, '');
                                            if (!isNaN(value) && value !== '') {
                                                $event.target.value = formatCurrency(value);
                                            } else {
                                                $event.target.value = '';
                                            }
                                        "
                                        class="w-full pl-12 pr-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200 text-lg font-medium"
                                        placeholder="0" required autocomplete="off">
                                    @error('cash_received')
                                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                {{-- Quick buttons --}}
                                <div class="grid grid-cols-3 gap-2 mt-3">
                                    @php $quickAmounts = [50000, 100000, 150000, 200000, 500000, 1000000]; @endphp
                                    @foreach ($quickAmounts as $amount)
                                        <button type="button"
                                            wire:click="$set('cash_received', '{{ number_format($amount, 0, '', '.') }}')"
                                            class="py-2 px-3 bg-gray-100 dark:bg-gray-700 rounded-lg text-sm font-medium text-gray-700 dark:text-white transition-colors duration-200 hover:bg-gray-200 dark:hover:bg-gray-600">
                                            {{ number_format($amount, 0, ',', '.') }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="mb-6">
                                <div class="rounded-xl p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-900/50">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-8 h-8 text-amber-600 dark:text-amber-400" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                            </path>
                                        </svg>
                                        <span class="text-sm text-amber-700 dark:text-amber-500">
                                            Pembayaran non-tunai akan diproses sesuai nominal total belanja
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- ================= BOTTOM BUTTONS ================= --}}
                <div class="flex gap-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" wire:click="$set('showCheckoutModal', false)"
                        class="flex-1 py-3 bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white font-medium rounded-xl transition-all duration-200 transform hover:scale-[0.98]">
                        Batal
                    </button>
                    <button type="submit" @if ($is_cash && ($change < 0 || empty($cash_received))) disabled @endif
                        class="flex-1 py-3 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-medium rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100">
                        <span>Bayar</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
    @if ($showConfirmationModal)
        <div wire:ignore.self
            class="fixed inset-0 bg-black/60 backdrop-blur-sm flex justify-center items-center z-50 p-4">
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md transform animate-modal-appear">
                <div class="p-6">
                    <div class="mx-auto w-20 h-20 mb-4">
                        <svg class="w-full h-full text-green-500 animate-success-check" fill="none"
                            viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"
                                class="animate-circle-draw" />
                            <path d="M7 12l3 3 7-7" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="animate-check-draw" />
                        </svg>
                    </div>

                    <h3 class="text-2xl font-bold text-gray-800 dark:text-white mb-2 text-center">
                        Pembayaran Berhasil!
                    </h3>

                    <p class="text-gray-600 dark:text-white text-center mb-6">
                        Transaksi telah berhasil diproses
                    </p>

                    <div class="bg-gray-50 dark:bg-gray-700 rounded-xl p-4 mb-6">
                        <div class="space-y-2 text-sm">
                            @if ($orderToPrint)
                                @php
                                    $order = \App\Models\Transaction::find($orderToPrint);
                                @endphp
                                @if ($order)
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 dark:text-gray-400">No. Transaksi</span>
                                        <span
                                            class="font-medium text-gray-800 dark:text-white">{{ $order->transaction_number }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 dark:text-gray-400">Total Belanja</span>
                                        <span class="font-medium text-gray-800 dark:text-white">Rp
                                            {{ number_format($order->total, 0, ',', '.') }}</span>
                                    </div>
                                    @if ($order->change > 0)
                                        <div class="flex justify-between">
                                            <span class="text-gray-600 dark:text-gray-400">Kembalian</span>
                                            <span class="font-medium text-green-600">Rp
                                                {{ number_format($order->change, 0, ',', '.') }}</span>
                                        </div>
                                    @endif
                                @endif
                            @endif
                        </div>
                    </div>

                    <div class="space-y-3">
                        <h4 class="text-sm font-medium text-gray-700 dark:text-white mb-2">
                            Cetak Struk?
                        </h4>

                        <div class="grid grid-cols-2 gap-3">
                            <a href="{{ url('/invoice/' . $orderToPrint . '/pdf') }}"
                                target="_blank" class="flex items-center justify-center space-x-2 py-3 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-medium rounded-xl transition-all duration-200 transform hover:scale-[0.98]">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                                    </path>
                                </svg>
                                <span>Cetak Struk</span>
                            </a>
                            <button wire:click="$set('showConfirmationModal', false)"
                                class="flex items-center justify-center space-x-2 py-3 bg-gradient-to-r from-red-500 to-pink-600 hover:from-red-600 hover:to-pink-700 text-white font-medium rounded-xl transition-all duration-200 transform hover:scale-[0.98]">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                <span>Lewati</span>
                            </button>
                        </div>
                    </div>

                    {{-- BLOK DUPLIKAT YANG ERROR SUDAH DIHAPUS DARI SINI --}}

                    <div class="mt-4 text-center text-xs text-gray-500 dark:text-gray-400" x-data="paymentSuccessTimer()">
                        Modal akan tertutup dalam <span x-text="seconds"></span> detik
                    </div>
                </div>
            </div>
        </div>

        <style>
            @keyframes circle-draw { to { stroke-dashoffset: 0; } }
            @keyframes check-draw { to { stroke-dashoffset: 0; } }
            @keyframes scale-up { to { transform: scale(1); } }
            .animate-circle-draw { stroke-dasharray: 62.83; stroke-dashoffset: 62.83; animation: circle-draw 0.6s ease-out forwards; }
            .animate-check-draw { stroke-dasharray: 24; stroke-dashoffset: 24; animation: check-draw 0.3s ease-out 0.6s forwards; }
            .animate-success-check { animation: scale-up 0.3s ease-out 0.9s forwards; transform: scale(0); }
        </style>
    @endif
</div>