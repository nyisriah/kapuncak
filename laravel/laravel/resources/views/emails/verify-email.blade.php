<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .container {
            background-color: #f9f9f9;
            border-radius: 8px;
            padding: 30px;
            border: 1px solid #ddd;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #2c3e50;
            margin: 0;
        }
        .content {
            color: #555;
            margin-bottom: 25px;
        }
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .button:hover {
            background-color: #2980b9;
        }
        .footer {
            text-align: center;
            border-top: 1px solid #ddd;
            padding-top: 20px;
            font-size: 12px;
            color: #999;
        }
        .link-text {
            word-break: break-all;
            font-size: 12px;
            color: #3498db;
            margin-top: 15px;
            padding: 10px;
            background-color: #f0f0f0;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>KawanPuncak</h1>
            <p>Verifikasi Email Anda</p>
        </div>

        <div class="content">
            <p>Halo {{ $user->name }},</p>
            <p>Terima kasih telah mendaftar di KawanPuncak. Untuk menyelesaikan proses pendaftaran, silakan verifikasi email Anda dengan mengklik tombol di bawah ini:</p>

            <div class="button-container">
                <a href="{{ $verificationUrl }}" class="button">Verifikasi Email</a>
            </div>

            <p>Atau salin link berikut ke browser Anda:</p>
            <div class="link-text">
                {{ $verificationUrl }}
            </div>

            <p>Link ini akan berlaku selama 24 jam.</p>
        </div>

        <div class="footer">
            <p>Jika Anda tidak mendaftar akun ini, abaikan email ini.</p>
            <p>&copy; {{ date('Y') }} KawanPuncak. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
