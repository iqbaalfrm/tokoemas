<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barcode Produk</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 8px;
            margin: 0;
            padding: 0;
        }
        
        .barcode-label {
            width: 2in; 
            height: 1.5in; 
            float: left;
            overflow: hidden;
            margin: 1px;
            border: 1px solid #000; 

            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center; 
            padding: 0;
        }
        
        .barcode-label img {
            width: 95%; 
            max-height: 40px; 
            display: block;
            margin: 0 auto;
            padding-top: 0; 
            margin-bottom: 0; 
        }
        
        .barcode-label p {
            font-size: 8px;
            margin: 0 0 1px;
            line-height: 1.1;
        }
        
        /* Gaya khusus untuk nomor barcode */
        .barcode-number {
            font-size: 11px !important; /* DITINGKATKAN */
            font-weight: bold;
            margin-top: 2px !important; /* Jarak dari barcode image */
            margin-bottom: 0 !important;
        }
        
        /* Gaya Khusus untuk Harga */
        .price-text {
            font-size: 14px !important; 
            margin-bottom: 3px !important; /* DIKURANGI jarak ke barcode image */
        }

        /* Gaya Khusus untuk Nama Produk */
        .name-text {
            font-size: 12px !important; /* DITINGKATKAN agar lebih jelas */
            font-weight: bold;
            margin-bottom: 1px !important; 
        }

    </style>
</head>
<body>
    
    @foreach ($barcodes as $barcode)
        
        <div class="barcode-label">
            <p class="name-text">{{ $barcode['name'] }}</p>
            <p class="price-text">Rp. {{ number_format($barcode['price'], 0, ',', '.') }}</p>
            <img src="{{ $barcode['barcode'] }}" alt="{{ $barcode['number'] }}">
            
            <p class="barcode-number">{{ $barcode['number'] }}</p>
        </div>

    @endforeach

</body>
</html>