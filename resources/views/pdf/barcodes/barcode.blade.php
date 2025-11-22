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
            border: 5px solid #000; 

            /* Flexbox untuk pemusatan */
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
            margin: 2px auto 0; /* Tambah sedikit margin atas gambar barcode */
        }
        
        .barcode-label p {
            font-size: 8px;
            margin: 0 0 1px; /* PERUBAHAN: Mengurangi margin atas dan bawah */
            line-height: 1.1; /* PERUBAHAN: Menyesuaikan tinggi baris */
        }
    </style>
</head>
<body>
    
    @foreach ($barcodes as $barcode)
        
        <div class="barcode-label">
            <p style="font-weight: bold; margin-bottom: 0;">{{ $barcode['name'] }}</p>
            <p style="margin-top: 0;">Rp. {{ number_format($barcode['price'], 0, ',', '.') }}</p>
            <img src="{{ $barcode['barcode'] }}" alt="{{ $barcode['number'] }}"><br>
            <p style="font-size: 7px; margin-top: 2px;">{{ $barcode['number'] }}</p>
        </div>

    @endforeach

</body>
</html>