<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password</title>
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
            background-color: #e74c3c;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .button:hover {
            background-color: #c0392b;
        }
        .footer {
            text-align: center;
            border-top: 1px solid #ddd;
            padding-top: 20px;
            font-size: 12px;
            color: #999;
        }
        .warning {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 10px;
            margin: 15px 0;
            border-radius: 3px;
            color: #856404;
        }
        .link-text {
            word-break: break-all;
            font-size: 12px;
            color: #e74c3c;
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
            <p>Reset Password Anda</p>
        </div>

        <div class="content">
            <p>Kami menerima permintaan untuk reset password akun Anda ({{ $userEmail }}).</p>
            
            <div class="warning">
                <strong>Penting:</strong> Link reset password ini hanya berlaku selama 60 menit.
            </div>

            <p>Klik tombol di bawah untuk membuat password baru:</p>

            <div class="button-container">
                <a href="{{ $resetUrl }}" class="button">Reset Password</a>
            </div>

            <p>Atau salin link berikut ke browser Anda:</p>
            <div class="link-text">
                {{ $resetUrl }}
            </div>

            <p>Jika Anda tidak meminta reset password, abaikan email ini.</p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} KawanPuncak. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
