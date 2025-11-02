<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice Transaksi - {{ $transaction->transaction_number ?? 'INV' }}</title>
    <style>
        @page { size: A5 landscape; margin: 10mm; }
        body { font-family: 'Poppins', sans-serif; font-size: 11px; color: #333; }
        .container { width: 100%; border: 1px solid #a00; border-radius: 5px; padding: 10px; }
        
        /* --- Header --- */
        .header { 
            overflow: auto; /* Bertindak sebagai "clearfix" untuk float */
        }
        .left {
            float: left;
            width: 55%; 
        }
        .right-info { 
            float: right;
            width: 45%; 
            text-align: right; 
            font-size: 10px; 
            line-height: 1.4; 
        }
        .logo { width: 100px; }
        .logo img { width: 100%; border-radius: 5px; }
        .toko-info { font-size: 10px; line-height: 1.3; }
        .toko-info b { color: #a00; }
        .barcode { margin-top: 3px; text-align: right; }

        /* --- Konten --- */
        .section-title { 
            font-weight: bold; 
            color: #a00; 
            margin-top: 10px; 
            margin-bottom: 5px; 
        }
        .table-barang { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 8px; 
        }
        .table-barang th, .table-barang td {
            border: 1px solid #a00; 
            padding: 4px 6px; 
            text-align: center;
        }
        .table-barang th {
            background-color: #a00; 
            color: white; 
            font-weight: bold;
        }
        .foto-produk {
            width: 75px;
            height: 75px;
            object-fit: cover;
            border: 1px solid #a00;
            border-radius: 5px;
        }

        /* --- Total --- */
        .harga { 
            font-size: 13px; 
            font-weight: bold; 
            color: #a00; 
        }

        /* --- Style untuk Footer & TTD Gabungan --- */
        .footer-ttd-table {
            width: 100%;
            margin-top: 10px; /* Jarak dari 'Terbilang' */
            border-collapse: collapse;
        }

        .footer-content {
            /* Kolom kiri untuk 'Perhatian' */
            width: 70%;
            font-size: 9px; 
            color: #555; 
            line-height: 1.3;
            vertical-align: top; /* Penting: Rata atas */
        }

        .ttd-content {
            /* Kolom kanan untuk 'Kasir' */
            width: 30%;
            text-align: center; /* Posisi TTD di tengah kolom ini */
            vertical-align: top; /* Penting: Rata atas */
        }
        
        .ttd-content p {
            margin: 2px 0;
        }

        .ttd-name {
            padding-top: 35px; /* Spasi untuk Tanda Tangan */
        }
        
    </style>
</head>
<body>
<div class="container">
    {{-- Header --}}
    <div class="header">
        <div class="left">
            <div class="logo">
          <img src="{{ public_path('images/logo1.jpg') }}" alt="Logo" style="width: 60px; height: auto;">
            </div>
            <div class="toko-info">
                <b>Toko Mas Hartono Wiyono</b><br>
                Ps. Wates, Jl. Diponegoro No. 16A, Wates, <br> 
                Kec. Wates, Kabupaten Kulon Progo, Daerah Istimewa Yogyakarta 55651<br>
                IG: @tokoemasarjuna<br>
                WhatsApp: 0812-3456-7890
            </div>
        </div>

        <div class="right-info">
            <b>Wates, {{ now()->format('d F Y') }}</b><br>
          Nama: {{ $transaction->member->nama ?? $transaction->name ?? '-' }}<br>
Alamat: {{ $transaction->member->alamat ?? $transaction->address ?? '-' }}<br>
No. Telepon: {{ $transaction->member->no_hp ?? $transaction->phone ?? '-' }}<br>
<b>No. Trans: {{ $transaction->transaction_number }}</b>
            <div class="barcode">
                <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($transaction->transaction_number, 'C128', 1.2, 40, [0,0,0], false) }}" alt="barcode" />
            </div>
        </div>
    </div>

    {{-- Detail Produk --}}
    <div class="section-title">Detail Barang</div>
    <table class="table-barang">
        <thead>
        <tr>
            <th>Foto</th>
            <th>Jenis</th>
            <th>Model</th>
            <th>Kadar</th>
            <th>Kode</th>
            <th>Berat</th>
            <th>Harga</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($transaction->items as $item)
            <tr>
                <td>
                    @if ($item->product->image)
                        <img src="{{ public_path('storage/' . $item->product->image) }}" class="foto-produk">
                    @else
                        <img src="{{ public_path('images/default-product.png') }}" class="foto-produk">
                    @endif
                </td>
                <td>{{ strtoupper($item->product->category->name ?? '-') }}</td>
                <td>{{ strtoupper($item->product->name) }}</td>
                <td>{{ $item->product->gold_type ?? '8K' }}</td>
                <td>{{ $item->product->kode_barang ?? '-' }}</td>
               <td>{{ number_format($item->weight_gram ?? 0, 3) }} Gr</td>
                <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    {{-- Total --}}
    <p style="text-align:right; margin-top:10px;">
        Total: <span class="harga">Rp {{ number_format($transaction->total, 0, ',', '.') }}</span>
    </p>
    <p><b>Terbilang:</b> {{ ucwords(terbilang($transaction->total)) }} Rupiah</p>

    {{-- Catatan & TTD (Digabung) --}}
    <table class="footer-ttd-table">
        <tr>
            <td class="footer-content">
                <b>Perhatian:</b><br>
                1. Barang, berat, kadar sudah diperiksa dengan benar oleh pembeli.<br>
                2. Perhiasan bisa dijual kembali sesuai harga pasar dikurangi ongkos.<br>
                3. Kecuali barang rusak, patah, atau berisi batu berbeda nilai.
            </td>
            <td class="ttd-content">
                <p>Kasir:</p>
                <p class="ttd-name"><b>{{ auth()->user()->name ?? 'Owner Toko' }}</b></p>
            </td>
        </tr>
    </table>
    
</div>
</body>
</html>