<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terjadi Kendala - {{ config('app.name', 'Toko Emas') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .error-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            max-width: 500px;
            width: 100%;
            padding: 50px 40px;
            text-align: center;
        }
        
        .error-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #fef3c7 0%, #fcd34d 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
        }
        
        .error-icon svg {
            width: 50px;
            height: 50px;
            color: #f59e0b;
        }
        
        .error-code {
            font-size: 48px;
            font-weight: 800;
            color: #ef4444;
            margin-bottom: 10px;
        }
        
        .error-title {
            font-size: 24px;
            color: #1f2937;
            margin-bottom: 15px;
            font-weight: 600;
        }
        
        .error-message {
            color: #6b7280;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        
        .error-id {
            background: #fef3c7;
            border: 1px solid #fcd34d;
            border-radius: 8px;
            padding: 12px 20px;
            margin-bottom: 25px;
            display: inline-block;
        }
        
        .error-id-label {
            font-size: 12px;
            color: #92400e;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        
        .error-id-value {
            font-family: 'Courier New', monospace;
            font-size: 14px;
            font-weight: 600;
            color: #78350f;
        }
        
        .error-actions {
            display: flex;
            flex-direction: column;
            gap: 12px;
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
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.5);
        }
        
        .btn-secondary {
            background: #f3f4f6;
            color: #374151;
        }
        
        .btn-secondary:hover {
            background: #e5e7eb;
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
        
        .help-text {
            margin-top: 20px;
            font-size: 13px;
            color: #9ca3af;
        }

        @media (max-width: 480px) {
            .error-code {
                font-size: 36px;
            }
            .error-container {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>
        
        <div class="error-code">500</div>
        <h1 class="error-title">Terjadi Kendala di Sistem</h1>
        <p class="error-message">
            Mohon maaf, terjadi kendala saat memproses permintaan Anda. 
            Tim kami sedang menangani masalah ini. Silakan coba beberapa saat lagi.
        </p>
        
        <div class="error-id">
            <div class="error-id-label">ID Error untuk Pelaporan</div>
            <div class="error-id-value">{{ strtoupper(substr(md5(time() . rand()), 0, 8)) }}</div>
        </div>
        
        <div class="error-actions">
            <a href="/" class="btn btn-primary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Kembali ke Beranda
            </a>
            
            <button onclick="window.location.reload()" class="btn btn-secondary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Coba Lagi
            </button>
        </div>
        
        <p class="help-text">
            Jika masalah berlanjut, hubungi admin dengan menyertakan ID Error di atas.
        </p>
        
        <div class="store-badge">
            <strong>{{ config('app.name', 'Toko Emas') }}</strong> 
            &bull; {{ env('STORE_CODE', 'store') }}
        </div>
    </div>
</body>
</html>
