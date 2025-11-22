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
            text-align: center;
            float: left;
            overflow: hidden;
            padding: 3px;
            margin: 1px;
            /* BARIS INI DITAMBAHKAN UNTUK GARIS TEPI */
            border: 1px solid #ccc;
        }
        
        .barcode-label img {
            width: 90%;
            max-height: 25px;
            display: block;
            margin: 0 auto;
        }
        
        .barcode-label p {
            font-size: 8px;
            margin: 1px 0;
            line-height: 1;
        }
    </style>
</head>
<body>
    
    @foreach ($barcodes as $barcode)
        
        <div class="barcode-label">
            <p style="font-weight: bold;">{{ $barcode['name'] }}</p>
            <p>Rp. {{ number_format($barcode['price'], 0, ',', '.') }}</p>
            <img src="{{ $barcode['barcode'] }}" alt="{{ $barcode['number'] }}"><br>
            <p style="font-size: 7px;">{{ $barcode['number'] }}</p>
        </div>

    @endforeach

</body>
</html>