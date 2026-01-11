<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sesi Berakhir - {{ config('app.name', 'Toko Emas') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
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
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
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
            color: #f59e0b;
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
            margin-bottom: 30px;
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
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(245, 158, 11, 0.5);
        }
        
        .btn-secondary {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
        }
        
        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.5);
        }
        
        .btn svg {
            width: 20px;
            height: 20px;
            margin-right: 8px;
        }
        
        .info-box {
            background: #fef3c7;
            border: 1px solid #fcd34d;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 25px;
            text-align: left;
        }
        
        .info-box-title {
            font-weight: 600;
            color: #92400e;
            font-size: 14px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
        }
        
        .info-box-title svg {
            width: 18px;
            height: 18px;
            margin-right: 8px;
        }
        
        .info-box ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }
        
        .info-box li {
            font-size: 13px;
            color: #78350f;
            padding: 4px 0;
            padding-left: 20px;
            position: relative;
        }
        
        .info-box li::before {
            content: "•";
            position: absolute;
            left: 6px;
            color: #f59e0b;
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
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        
        <div class="error-code">419</div>
        <h1 class="error-title">Sesi Anda Telah Berakhir</h1>
        <p class="error-message">
            Demi keamanan, sesi Anda telah berakhir karena tidak ada aktivitas. 
            Silakan refresh halaman atau login kembali untuk melanjutkan.
        </p>
        
        <div class="info-box">
            <div class="info-box-title">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Mengapa ini terjadi?
            </div>
            <ul>
                <li>Anda tidak aktif terlalu lama</li>
                <li>Halaman dibuka di tab lain dan logout</li>
                <li>Browser menghapus cookie sesi</li>
            </ul>
        </div>
        
        <div class="error-actions">
            <button onclick="window.location.reload()" class="btn btn-primary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Refresh Halaman
            </button>
            
            <a href="/login" class="btn btn-secondary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                </svg>
                Login Kembali
            </a>
        </div>
        
        <div class="store-badge">
            <strong>{{ config('app.name', 'Toko Emas') }}</strong> 
            &bull; {{ env('STORE_CODE', 'store') }}
        </div>
    </div>
</body>
</html>
