<?php

namespace App\Livewire;

use App\Helpers\TransactionHelper;
use App\Models\Category;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Services\DirectPrintService;
use Filament\Notifications\Notification;
use Livewire\Component;
use Livewire\WithPagination;

class Pos extends Component
{
    use WithPagination;

    public int | string $perPage = 10;
    public $categories;
    public $selectedCategory;
    public $search = '';
    public $print_via_bluetooth = false;
    public $barcode = '';
    public $name = 'Umum';
    public $payment_method_id;
    public $payment_methods;
    public array $order_items = [];
    public $total_price = 0;
    public $cash_received = '';
    public $change = 0;
    public $showConfirmationModal = false;
    public $showCheckoutModal = false;
    public $orderToPrint = null;
    public $is_cash = true;
    public $selected_payment_method = null;

    protected $listeners = [
        'scanResult' => 'handleScanResult',
    ];

    public function mount()
    {
        $settings = Setting::first();
        $this->print_via_bluetooth = $settings->print_via_bluetooth ?? false;
        $this->categories = collect([['id' => null, 'name' => 'Semua']])->merge(Category::all());
        if (session()->has('orderItems')) {
            $this->order_items = session('orderItems');
            $this->calculateTotal();
        }
        $this->payment_methods = PaymentMethod::all();
    }

    public function render()
    {
        return view('livewire.pos', [
            'products' => Product::where('stock', '>', 0)->where('is_active', 1)
                ->when($this->selectedCategory, fn ($query) => $query->where('category_id', $this->selectedCategory))
                ->where(function ($query) {
                    $query->where('name', 'LIKE', '%' . $this->search . '%')
                          ->orWhere('sku', 'LIKE', '%' . $this->search . '%');
                })
                ->paginate($this->perPage),
        ]);
    }

    // <-- DITAMBAHKAN: Hook untuk menangani harga yang diedit di keranjang
    public function updatedOrderItems()
    {
        $this->syncCart();
    }

    public function updatedPaymentMethodId($value)
    {
        if ($value) {
            $paymentMethod = PaymentMethod::find($value);
            $this->selected_payment_method = $paymentMethod;
            $this->is_cash = $paymentMethod->is_cash ?? false;
            
            if (!$this->is_cash) {
                // <-- DIUBAH: Gunakan number_format agar konsisten dengan view
                $this->cash_received = number_format($this->total_price, 0, ',', '.');
                $this->change = 0;
            } else {
                $this->calculateChange();
            }
        }
    }

    public function updatedCashReceived($value)
    {
        if ($this->is_cash) {
            $this->cash_received = $value;
            $this->calculateChange();
        }
    }

    public function calculateChange()
    {
        $cashReceived = $this->getCashReceivedNumeric();
        $totalPrice = floatval($this->total_price);
        
        $this->change = ($cashReceived >= $totalPrice) ? $cashReceived - $totalPrice : 0;
    }

    public function getCashReceivedNumeric()
    {
        return floatval(str_replace('.', '', $this->cash_received));
    }

    public function updatedBarcode($barcode)
    {
        if(empty($barcode)) return;
        $product = Product::where('barcode', $barcode)->where('is_active', true)->first();
        if ($product) {
            $this->addToOrder($product->id);
        } else {
            Notification::make()->title('Produk tidak ditemukan ' . $barcode)->danger()->send();
        }
        $this->barcode = '';
    }

    public function handleScanResult($decodedText)
    {
        $this->updatedBarcode($decodedText);
    }

    public function setCategory($categoryId = null)
    {
        $this->selectedCategory = $categoryId;
        $this->resetPage(); // <-- DITAMBAHKAN: Agar kembali ke halaman 1 saat ganti kategori
    }

    public function addToOrder($productId)
    {
        $product = Product::find($productId);
        if ($product) {
            // <-- DIOPTIMALKAN: Cek langsung menggunakan key array, lebih cepat
            if (isset($this->order_items[$productId])) {
                if ($this->order_items[$productId]['quantity'] >= $product->stock) {
                    Notification::make()->title('Stok barang tidak mencukupi')->danger()->send();
                    return;
                }
                $this->order_items[$productId]['quantity']++;
            } else {
                // <-- DIOPTIMALKAN: Gunakan ID produk sebagai key array
                $this->order_items[$productId] = [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'selling_price' => $product->selling_price,
                    'cost_price' => $product->cost_price,
                    'image_url' => $product->image,
                    'quantity' => 1,
                ];
            }
            $this->syncCart();
        }
    }

    public function increaseQuantity($productId)
    {
        $product = Product::find($productId);
        if (!$product) {
            Notification::make()->title('Produk tidak ditemukan')->danger()->send();
            return;
        }

        // <-- DIOPTIMALKAN: Akses langsung tanpa loop
        if (isset($this->order_items[$productId])) {
            if ($this->order_items[$productId]['quantity'] + 1 <= $product->stock) {
                $this->order_items[$productId]['quantity']++;
            } else {
                Notification::make()->title('Stok barang tidak mencukupi')->danger()->send();
            }
        }
        $this->syncCart();
    }

    public function decreaseQuantity($productId)
    {
        // <-- DIOPTIMALKAN: Akses langsung tanpa loop
        if (isset($this->order_items[$productId])) {
            if ($this->order_items[$productId]['quantity'] > 1) {
                $this->order_items[$productId]['quantity']--;
            } else {
                unset($this->order_items[$productId]);
            }
        }
        $this->syncCart();
    }
    
    // <-- DITAMBAHKAN: Helper function agar tidak mengulang kode
    private function syncCart()
    {
        session()->put('orderItems', $this->order_items);
        $this->calculateTotal();
        if ($this->is_cash && !empty($this->cash_received)) {
            $this->calculateChange();
        }
    }

    public function calculateTotal()
    {
        $total = 0;
        foreach ($this->order_items as $item) {
            // <-- DIUBAH: Gunakan 'selling_price' dan tambahkan pengecekan
            $price = $item['selling_price'] ?? 0;
            $quantity = $item['quantity'] ?? 0;
            $total += $quantity * $price;
        }
        $this->total_price = $total;
        return $total;
    }

    public function resetOrder()
    {
        session()->forget('orderItems');
        // <-- DIOPTIMALKAN: Gunakan reset() bawaan Livewire
        $this->reset([
            'order_items', 'payment_method_id', 'total_price', 'cash_received', 
            'change', 'is_cash', 'selected_payment_method', 'name'
        ]);
        $this->name = 'Umum'; // Kembalikan ke nilai default
    }

    public function checkout()
    {
        $this->validate([
            'payment_method_id' => 'required',
            'name' => 'string|max:255',
        ], [
            'payment_method_id.required' => 'Metode pembayaran harus dipilih',
        ]);
        
        if ($this->is_cash) {
            if (empty($this->cash_received)) {
                $this->addError('cash_received', 'Nominal bayar harus diisi');
                return;
            }
            if ($this->getCashReceivedNumeric() < $this->total_price) {
                $this->addError('cash_received', 'Nominal bayar kurang dari total belanja');
                return;
            }
        }

        if (empty($this->order_items)) {
            Notification::make()->title('Keranjang kosong')->danger()->send();
            $this->showCheckoutModal = false;
            return;
        }

        $order = Transaction::create([
            'payment_method_id' => $this->payment_method_id,
            'transaction_number' => TransactionHelper::generateUniqueTrxId(),
            'name' => $this->name,
            'total' => $this->total_price,
            'cash_received' => $this->is_cash ? $this->getCashReceivedNumeric() : $this->total_price,
            'change' => $this->change,
        ]);

        foreach ($this->order_items as $item) {
            // <-- DIUBAH: Hitung profit final di sini
            $finalProfit = ($item['selling_price'] - $item['cost_price']) * $item['quantity'];

            TransactionItem::create([
                'transaction_id' => $order->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['selling_price'], // <-- DIUBAH
                'cost_price' => $item['cost_price'],
                'total_profit' => $finalProfit, // <-- DIUBAH
            ]);
        }

        $this->orderToPrint = $order->id;
        $this->showConfirmationModal = true;
        $this->showCheckoutModal = false;
        Notification::make()->title('Order berhasil disimpan')->success()->send();
        $this->resetOrder();
    }

    public function printLocalKabel()
    {
        $directPrint = app(DirectPrintService::class);
        $directPrint->print($this->orderToPrint);
        $this->showConfirmationModal = false;
        $this->orderToPrint = null;
    }

    public function printBluetooth()
    {
        $order = Transaction::with(['paymentMethod', 'transactionItems.product'])->findOrFail($this->orderToPrint);
        $this->dispatch(
            'doPrintReceipt',
            store: Setting::first(),
            order: $order,
            items: $order->transactionItems,
            date: $order->created_at->format('d-m-Y H:i:s')
        );
        $this->showConfirmationModal = false;
        $this->orderToPrint = null;
    }
}