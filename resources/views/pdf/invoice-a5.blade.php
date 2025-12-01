<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice - {{ $transaction->transaction_number }}</title>
    <style>
        @page { 
            size: A5 landscape; 
            margin: 0; 
        }
        
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9pt; /* Ukuran font besar agar jelas */
            margin: 0;
            padding: 0;
            color: #333;
            /* BACKGROUND IMAGE */
            /* Pastikan gambar ini adalah TEMPLATE KOSONG (tanpa tulisan data) */
            background-image: url("{{ public_path('images/bg.png') }}");
            background-size: 100% 100%;
            background-repeat: no-repeat;
            background-position: center;
        }

        .container {
            /* PENTING: Padding ini mengatur jarak tulisan dari pinggir kertas */
            /* Sesuaikan angka ini jika tulisan menabrak hiasan emas background */
            /* Atas: 35px, Kanan: 25px, Bawah: 20px, Kiri: 25px */
            padding: 35px 25px 20px 25px; 
            position: relative;
        }

        /* HEADER */
        .header-table {
            width: 100%;
            margin-bottom: 5px;
        }

        .logo-img {
            /* Jika di background sudah ada logo, bagian ini bisa dihapus/dikomentari */
            /* Tapi sebaiknya tetap ada agar tajam saat diprint */
            height: 50px; 
            margin-bottom: 2px;
        }

        .shop-name {
            font-family: 'Times New Roman', serif;
            font-size: 18pt;
            font-weight: bold;
            font-style: italic;
            color: #b8860b;
            margin-top: 0;
            text-shadow: 1px 1px 0 #fff; /* Shadow putih agar terbaca di atas background */
        }

        .shop-address {
            font-size: 9pt;
            line-height: 1.2;
            color: #333;
            /* Background transparan putih biar teks alamat jelas */
            background-color: rgba(255, 255, 255, 0.6);
            display: inline-block;
            padding: 2px;
            border-radius: 3px;
        }

        .customer-info {
            text-align: right;
            font-size: 9pt;
            line-height: 1.3;
            vertical-align: top;
            padding-top: 5px;
        }

        .barcode-img {
            margin-top: 2px;
            height: 28px;
            background-color: #fff;
            padding: 2px;
        }

        .section-title {
            text-align: center;
            font-weight: bold;
            color: #b00;
            margin-top: 5px;
            margin-bottom: 5px;
            font-size: 10pt;
            text-transform: uppercase;
        }

        /* TABEL */
        table.items {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #b8860b;
            /* Background tabel agak putih agar tulisan tabel tidak tabrakan dengan pola background */
            background-color: rgba(255, 255, 255, 0.85);
            margin-bottom: 5px;
        }

        table.items th {
            background-color: #fff;
            color: #b00;
            border: 1px solid #b8860b;
            padding: 5px;
            font-size: 9pt;
            font-weight: bold;
            text-align: center;
        }

        table.items td {
            border: 1px solid #b8860b;
            padding: 5px;
            font-size: 9pt;
            vertical-align: middle;
            text-align: center;
        }
        
        /* FOOTER */
        .footer-info-table {
            width: 100%;
            margin-top: 5px;
            font-size: 8pt;
        }

        .terms {
            vertical-align: top;
            padding-right: 10px;
            line-height: 1.3;
        }

        .signatures {
            vertical-align: top;
            text-align: center;
            width: 30%;
        }

        .text-red { 
            color: #b00; 
            font-weight: bold; 
        }
        
        .total-row td {
            border: 1px solid #b8860b;
            font-weight: bold;
            background-color: #fcfcfc;
            padding: 6px;
        }
    </style>
</head>
<body>

    <div class="container">
        
        <!-- BAGIAN HEADER (Logo, Nama Toko, Data Customer) -->
        <table class="header-table">
            <tr>
                <td width="60%" valign="top">
                    <img src="{{ public_path('images/logo.png') }}" class="logo-img" alt="Logo">
                    
                    <div class="shop-name">Harto Wiyono</div>
                    <div class="shop-address">
                        <span class="text-red">Toko Mas Harto Wiyono</span><br>
                        Ps. Wates, Jl. Diponegoro No. 16A, Wates,<br>
                        Kec. Wates, Kab. Kulon Progo, DIY 55651<br>
                        IG: @tokomashartonowiyono | WA: 0812-3456-7890
                    </div>
                </td>
                <td width="40%" class="customer-info">
                    <strong>Wates, {{ now()->format('d F Y') }}</strong><br>
                    Nama: {{ $transaction->member->nama ?? $transaction->name ?? 'Umum' }}<br>
                    Alamat: {{ $transaction->member->alamat ?? $transaction->address ?? '-' }}<br>
                    No. Telp: {{ $transaction->member->no_hp ?? $transaction->phone ?? '-' }}<br>
                    
                    <div style="margin-top: 2px;">
                        <b>No. Trans: {{ $transaction->transaction_number }}</b><br>
                        @if(isset($barcode))
                            <img src="data:image/png;base64,{{ $barcode }}" class="barcode-img">
                        @endif
                    </div>
                </td>
            </tr>
        </table>

        <div class="section-title">Detail Barang</div>

        <!-- BAGIAN TABEL -->
        <table class="items">
            <thead>
                <tr>
                    <th width="12%">Foto</th>
                    <th width="15%">Jenis</th>
                    <th width="33%">Model</th>
                    <th width="10%">Kadar</th>
                    <th width="10%">Berat</th>
                    <th width="20%">Harga</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transaction->items as $item)
                <tr>
                    <td>
                        @if ($item->product->image)
                            <img src="{{ public_path('storage/' . $item->product->image) }}" style="width: 40px; height: 40px; object-fit: cover; border: 1px solid #ccc; border-radius: 3px;">
                        @else
                            <div style="width: 40px; height: 40px; border: 1px solid #ccc; border-radius: 3px; margin: 0 auto; line-height: 40px; color: #ccc; font-size: 8px;">No Pic</div>
                        @endif
                    </td>
                    
                    <td>{{ $item->product->subCategory->category->name ?? $item->product->type ?? '-' }}</td>
                    <td>
                        {{ $item->product->name }}<br>
                        <small style="color: #555;">({{ $item->product->sku ?? '-' }})</small>
                    </td>
                    <td>{{ $item->product->gold_karat ?? $item->product->carat ?? '-' }}</td>
                    <td>{{ number_format($item->weight_gram ?? $item->quantity, 2) }} gr</td>
                    <td style="text-align: right;">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                </tr>
                @endforeach

                <!-- Baris Kosong (Tinggi dikurangi agar muat) -->
                @for($i = 0; $i < (3 - count($transaction->items)); $i++)
                <tr style="height: 20px;"> 
                    <td></td><td></td><td></td><td></td><td></td><td></td>
                </tr>
                @endfor
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="5" style="text-align: right; border: none; padding-right: 10px;">Total:</td>
                    <td style="text-align: right;">Rp {{ number_format($transaction->total, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        <!-- BAGIAN FOOTER -->
        <table class="footer-info-table">
            <tr>
                <td class="terms" width="65%">
                    <strong>Terbilang:</strong><br>
                    <span style="font-style: italic; font-weight: bold;">
                         {{ ucwords(terbilang($transaction->total)) }} Rupiah 
                    </span>
                    
                    <br><br>
                    <strong>Perhatian:</strong>
                    <ol style="padding-left: 12px; margin-top: 1px; font-size: 8pt; line-height: 1.2; margin-bottom: 0;">
                        <li>NOTA INI WAJIB DISIMPAN BAIK-BAIK. APABILA BARANG INGIN DIJUAL / DITUKAR, NOTA INI WAJIB DIBAWA.</li>
                        <li>Barang yang dibeli sudah diperiksa dengan benar oleh Pembeli berupa berat dan kadarnya.</li>
                        <li>Apabila ada kekeliruan akibat kekhilafan kadar/berat dapat ditukar kepada kami.</li>
                        <li>Barang ini jika dijual akan dibeli menurut harga dibawah pasar & dipotong ongkos kecuali barang yang mengandung batu, patri, dan rusak menurut harga yang berbeda.</li>
                    </ol>
                </td>
                
                <td class="signatures">
                    <table width="100%">
                        <tr>
                            <td align="center" width="50%">Kasir</td>
                        </tr>
                        <tr>
                            <!-- Tempat tanda tangan -->
                            <td align="center" height="40"></td> 
                        </tr>
                        <tr>
                            <td align="center">( ____________________ )</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

    </div>
</body>
</html>