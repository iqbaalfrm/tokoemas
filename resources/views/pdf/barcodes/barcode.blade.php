<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barcode Produk</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 0;
        }
        
        .barcode-label {
            width: 1.5in; 
            height: 1in; 
            float: left;
            overflow: hidden;
            margin: 1px;
            border: 1px solid #000; 

            display: flex;
            flex-direction: column;
            /* Flexbox diaktifkan untuk pemusatan */
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
            /* Hapus padding-top dan biarkan margin diatur oleh p atau Flexbox */
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
            font-size: 14px !important; 
            font-weight: bold;
            margin-top: 1px !important; 
            margin-bottom: 0 !important;
        }
        
        /* Gaya Khusus untuk Harga */
        .price-text {
            font-size: 14px !important; /* Dibuat besar agar seimbang dengan nama produk */
            margin-bottom: 5px !important; /* Tambah margin di bawah harga untuk jarak ke barcode */
        }

        /* Gaya Khusus untuk Nama Produk */
        .name-text {
            font-size: 14px !important;
            font-weight: bold;
            margin-bottom: 1px !important; /* Jarak dekat ke harga */
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