<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sedang Perawatan - {{ config('app.name', 'Toko Emas') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .maintenance-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            max-width: 500px;
            width: 100%;
            padding: 50px 40px;
            text-align: center;
        }
        
        .maintenance-icon {
            width: 120px;
            height: 120px;
            margin: 0 auto 25px;
        }
        
        .maintenance-icon svg {
            width: 100%;
            height: 100%;
            color: #6366f1;
        }
        
        .gear {
            animation: spin 4s linear infinite;
            transform-origin: center;
        }
        
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        .maintenance-title {
            font-size: 28px;
            color: #1f2937;
            margin-bottom: 15px;
            font-weight: 700;
        }
        
        .maintenance-message {
            color: #6b7280;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 25px;
        }
        
        .time-estimate {
            background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
            border: 1px solid #c7d2fe;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
        }
        
        .time-estimate-label {
            font-size: 13px;
            color: #6366f1;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }
        
        .time-estimate-value {
            font-size: 18px;
            font-weight: 600;
            color: #4338ca;
        }
        
        .progress-bar {
            background: #e5e7eb;
            border-radius: 10px;
            height: 8px;
            overflow: hidden;
            margin-top: 15px;
        }
        
        .progress-value {
            background: linear-gradient(90deg, #6366f1, #8b5cf6);
            height: 100%;
            width: 60%;
            border-radius: 10px;
            animation: pulse 2s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
        
        .contact-info {
            background: #f9fafb;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .contact-info p {
            font-size: 14px;
            color: #6b7280;
        }
        
        .contact-info a {
            color: #6366f1;
            text-decoration: none;
            font-weight: 600;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 14px 28px;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
            background: #25D366;
            color: white;
        }
        
        .btn:hover {
            background: #128C7E;
            transform: translateY(-2px);
        }
        
        .btn svg {
            width: 20px;
            height: 20px;
            margin-right: 8px;
        }
        
        .store-badge {
            margin-top: 30px;
            padding: 8px 16px;
            background: #f3f4f6;
            border-radius: 20px;
            display: inline-block;
            font-size: 12px;
            color: #6b7280;
        }
        
        .store-badge strong {
            color: #374151;
        }

        @media (max-width: 480px) {
            .maintenance-title {
                font-size: 22px;
            }
            .maintenance-container {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="maintenance-container">
        <div class="maintenance-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path class="gear" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        
        <h1 class="maintenance-title">Sedang Perawatan Sistem</h1>
        <p class="maintenance-message">
            Kami sedang melakukan peningkatan sistem untuk memberikan layanan yang lebih baik. 
            Mohon maaf atas ketidaknyamanan ini.
        </p>
        
        <div class="time-estimate">
            <div class="time-estimate-label">Perkiraan Waktu Kembali</div>
            <div class="time-estimate-value">Beberapa saat lagi</div>
            <div class="progress-bar">
                <div class="progress-value"></div>
            </div>
        </div>
        
        <div class="contact-info">
            <p>Ada pertanyaan mendesak? Hubungi kami di <a href="https://wa.me/6281234567890">WhatsApp</a></p>
        </div>
        
        <a href="https://wa.me/6281234567890?text=Halo,%20kapan%20sistem%20{{ urlencode(config('app.name', 'Toko Emas')) }}%20kembali%20online?" 
           class="btn" target="_blank">
            <svg fill="currentColor" viewBox="0 0 24 24">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
            </svg>
            Hubungi Admin
        </a>
        
        <div class="store-badge">
            <strong>{{ config('app.name', 'Toko Emas') }}</strong> 
            &bull; {{ env('STORE_CODE', 'store') }}
        </div>
    </div>
</body>
</html>
