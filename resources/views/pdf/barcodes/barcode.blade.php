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
            border: 3px solid #000; 

            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center; 
            padding: 0;
        }
        
        .barcode-label img {
            width: 90%;
            max-height: 25px;
            display: block;
            margin: 0 auto;
            padding-top: 5px; 
            /* Hapus margin-bottom agar angka kode menempel rapat */
            margin-bottom: 0; 
        }
        
        .barcode-label p {
            font-size: 8px;
            margin: 0 0 1px;
            line-height: 1.1;
        }
        
        /* Gaya khusus untuk nomor barcode */
        .barcode-number {
            font-size: 11px !important; /* Dibuat lebih besar */
            font-weight: bold;
            margin-top: 1px !important; /* Dibuat menempel atau sangat dekat dengan gambar barcode */
            margin-bottom: 0 !important;
        }
    </style>
</head>
<body>
    
    @foreach ($barcodes as $barcode)
        
        <div class="barcode-label">
            <p style="font-weight: bold; margin-bottom: 0;">{{ $barcode['name'] }}</p>
            <p style="margin-top: 0;">Rp. {{ number_format($barcode['price'], 0, ',', '.') }}</p>
            <img src="{{ $barcode['barcode'] }}" alt="{{ $barcode['number'] }}">
            
            <p class="barcode-number">{{ $barcode['number'] }}</p>
        </div>

    @endforeach

</body>
</html>