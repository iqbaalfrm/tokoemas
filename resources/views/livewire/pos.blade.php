<div class="min-h-screen p-2 lg:p-6" style="font-family: 'Poppins'">
    <div class="max-w-[1600px] mx-auto">
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <div class="xl:col-span-2">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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

                        <div class="flex gap-3">
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
                                class="px-4 py-3 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                                <img src="{{ asset('images/qrcode-scan.svg') }}" class="w-6 h-6" alt="Scan QR Code"/>
                            </x-filament::button>
                        </div>
                    </div>
                    {{-- MODAL SCAN CAMERA --}}
                    <livewire:scanner-modal-component>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 mb-6">
                    <div class="overflow-x-auto">
                        <div class="flex gap-3 pb-2 whitespace-nowrap">
                            @foreach ($categories as $item)
                                <button wire:click="setCategory({{ $item['id'] ?? null }})"
                                    class="category-btn px-6 py-3 rounded-xl font-medium transition-all duration-300 transform hover:scale-105
                                    {{ $selectedCategory === $item['id']
                                        ? 'bg-gradient-to-r from-green-500 to-green-600 text-white shadow-lg'
                                        : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-white' }}">
                                    {{ $item['name'] }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 relative"> <div wire:loading wire:target="search, selectedCategory" class="absolute inset-0 bg-white/50 dark:bg-gray-800/50 backdrop-blur-sm flex items-center justify-center z-10 rounded-2xl">
                        <x-filament::loading-indicator class="h-10 w-10" />
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 2xl:grid-cols-5 gap-4">
                        @forelse ($products as $item)
                            <div wire:click="addToOrder({{ $item->id }})"
                                class="group bg-gray-50 dark:bg-gray-700 rounded-xl shadow-md hover:shadow-2xl transform hover:scale-105 transition-all duration-300 cursor-pointer overflow-hidden">
                                <div class="relative">
                                    <img src="{{ $item->image ? asset('storage/' . $item->image) : asset('images/default-product.png') }}" alt="{{ $item->name }}"
                                        class="w-full h-32 object-cover">
                                </div>
                                <div class="p-4">
                                    <h3 class="text-sm font-semibold text-gray-800 dark:text-white line-clamp-2 mb-2">
                                        {{ $item->name }}</h3>
                                    <div class="flex items-center justify-between w-full">
                                        <p class="text-lg font-bold text-green-600 dark:text-green-400">
                                            Rp {{ number_format($item->selling_price, 0, ',', '.') }}
                                        </p>
                                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
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

            <div class="xl:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 sticky top-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-gray-800 dark:text-white">Keranjang</h2>
                        <button wire:click="resetOrder"
                            class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                            Reset
                        </button>
                    </div>

                    @if (empty($order_items))
                        <div class="flex flex-col items-center justify-center py-12">
                            <img src="{{ asset('images/cart-empty.png') }}" alt="Empty Cart"
                                class="w-24 h-24 mb-4 opacity-50">
                            <p class="text-gray-500 dark:text-gray-400 font-medium">Keranjang Kosong</p>
                            <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Pilih produk untuk memulai</p>
                        </div>
                    @else
                        <div
                            class="{{ count($order_items) >= 4 ? 'max-h-[400px] overflow-y-auto pr-2' : '' }} space-y-3">
                            @foreach ($order_items as $item)
                                <div wire:key="{{ $item['product_id'] }}"
                                    class="bg-gray-50 dark:bg-gray-700 rounded-xl p-3 transition-all duration-200 hover:shadow-md">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $item['image_url'] ? asset('storage/' . $item['image_url']) : asset('images/default-product.png') }}"
                                            alt="{{ $item['name'] }}"
                                            class="w-16 h-16 object-cover rounded-lg shadow-sm">
                                        <div class="flex-1 min-w-0">
                                            <h4
                                                class="text-sm font-semibold text-gray-800 dark:text-white line-clamp-1">
                                                {{ $item['name'] }}
                                            </h4>
                                            
                                        <div class="relative mt-1"
   x-data="{
        price: @js($item['selling_price']),
        formatCurrency(value) {
            if (!value) return '';
            return parseInt(value).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }
   }"
   x-init="$watch('price', value => $wire.set('order_items.{{ $item['product_id'] }}.selling_price', value, true))"
>
    <span class="absolute left-0 top-1/2 -translate-y-1/2 text-sm text-gray-500 dark:text-gray-400">Rp</span>
    <input type="text"
           x-model="price"
           x-on:input.debounce.500ms="price = $event.target.value.replace(/\./g, '')"
           x-bind:value="formatCurrency(price)"
           x-init="$el.value = formatCurrency(price)"
           class="w-full bg-transparent border-0 border-b-2 border-gray-300 dark:border-gray-600 pl-6 p-1 text-sm font-semibold text-gray-600 dark:text-white focus:ring-0 focus:border-green-500 transition">
</div>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between mt-3">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">
                                            Subtotal: Rp
                                            {{ number_format($item['selling_price'] * $item['quantity'], 0, ',', '.') }}
                                        </span>
                                        <div class="flex items-center gap-2">
                                            <button wire:click="decreaseQuantity({{ $item['product_id'] }})"
                                                class="w-8 h-8 rounded-lg bg-gray-200 dark:bg-gray-600 hover:bg-white dark:hover:bg-gray-500 text-gray-700 dark:text-white font-medium transition-colors duration-200">
                                                -
                                            </button>
                                            <span class="w-10 text-center font-semibold text-gray-800 dark:text-white">
                                                {{ $item['quantity'] }}
                                            </span>
                                            <button wire:click="increaseQuantity({{ $item['product_id'] }})"
                                                class="w-8 h-8 rounded-lg bg-green-500 hover:bg-green-600 text-white font-medium transition-colors duration-200">
                                                +
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6 pt-6 border-t-2 border-gray-200 dark:border-gray-700">
                            <div class="flex justify-between items-center mb-4">
                                <span class="text-lg font-medium text-gray-600 dark:text-gray-400">Total</span>
                                <span class="text-2xl font-bold text-gray-800 dark:text-white">
                                    Rp {{ number_format($this->calculateTotal(), 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    @endif

                    <button type="button" wire:click="$set('showCheckoutModal', true)"
                        class="w-full mt-4 py-4 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200 {{ empty($order_items) ? 'opacity-50 cursor-not-allowed' : '' }}"
                        {{ empty($order_items) ? 'disabled' : '' }}>
                        <div class="flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                            Checkout
                        </div>
                    </button>
                </div>
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
}" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex justify-center items-center z-50 p-4">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-2xl transform scale-95 animate-modal-appear">
        <div class="p-6">
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
                            
                            <a href="http://127.0.0.1:8000/invoice/{{ $orderToPrint }}/pdf"
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