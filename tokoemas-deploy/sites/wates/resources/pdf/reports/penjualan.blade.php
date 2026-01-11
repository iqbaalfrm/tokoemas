<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Laporan Order</title>
    <style>
        body {
            margin: 0 auto;
            font-family: Arial, sans-serif;
            background: #FFFFFF;
            font-size: 12px;
            color: #001028;
        }

        header {
            padding: 10px 0;
            text-align: center;
            border-bottom: 1px solid #5D6975;
            margin-bottom: 20px;
        }

        #logo img {
            width: 120px;
        }

         h1 {
            font-size: 2em;
            margin: 14px 0;
        }

        span {
            font-size: 14px;
            color: #5D6975;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th, table td {
            border: 1px solid #C1CED9;
            padding: 8px;
            text-align: center;
        }

        table th {
            background-color: #F5F5F5;
            color: #5D6975;
        }

        .desc {
            text-align: left;
        }

        footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            height: 30px;
            border-top: 1px solid #C1CED9;
            text-align: center;
            padding: 8px 0;
            font-size: 0.8em;
            color: #5D6975;
        }
    </style>
</head>

<body>

    <header>
        <div id="logo">
            <img src="{{ storage_path('app/public/' . $logo) }}" alt="{{ asset('storage/' . $logo) }}">
        </div>
        <h1>Laporan Transaksi<br><span>{{ '(' . $fileName . ')' }}</span></h1>
    </header>

    <main>
    <?php $total_Order_amount = 0?>
        @foreach($data as $order)
        <table>
            <thead>
                <tr>
                    <th>No. Transaksi</th>
                    <th>Nama Pembeli</th>
                    <th>Nama Barang</th>
                    <th>Pembayaran</th>
                    <th>Total Harga</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $order->transaction_number }}</td>
                    <td>{{ $order->member->nama ?? $order->name ?? 'Umum' }}</td>
                    <td>
                        @php
                            $productNames = [];
                            foreach($order->transactionItems as $item) {
                                $productNames[] = $item->product?->name ?? 'Produk Tidak Ditemukan';
                            }
                            $productList = implode(', ', array_filter($productNames));
                        @endphp
                        {{ $productList ?: 'Tidak Ada Produk' }}
                    </td>
                    <td>{{ $order->paymentMethod->name ?? 'N/A' }}</td>
                    <td>Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
        <?php $total_Order_amount += $order->total ?>
        @endforeach

        <table>
            <thead>
                <tr>
                    <th style="background-color:white; color:black; font-size:16px">Total Uang Masuk: Rp {{ number_format( $total_Order_amount, 0, ',', '.') }}</th>
                </tr>
            </thead>
        </table>
    </main>

    <footer>
        Laporan ini dihasilkan secara otomatis tanpa tanda tangan.
    </footer>

</body>

</html>
