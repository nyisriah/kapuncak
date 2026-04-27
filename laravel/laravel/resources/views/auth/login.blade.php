<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Kawan Puncak</title>
    <style>
        :root {
            --primary: #10b981;
            --primary-dark: #059669;
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: var(--primary);
            min-height: 100vh;
            display: grid;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        
        .login-header {
            margin-bottom: 30px;
        }
        
        .login-header h1 {
            color: var(--primary-dark);
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        
        .login-header p {
            color: #666;
            font-size: 14px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group input {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: white;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }
        
        .form-group input::placeholder {
            color: #9ca3af;
        }
        
        .login-btn {
            width: 100%;
            background-color: var(--primary);
            color: white;
            border: none;
            padding: 14px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-bottom: 20px;
        }
        
        .login-btn:hover {
            background-color: var(--primary-dark);
        }
        
        .links {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        
        .links a {
            color: var(--primary-dark);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
        }
        
        .register-link {
            text-align: center;
            color: #666;
            font-size: 14px;
        }
        
        .register-link a {
            color: var(--primary-dark);
            font-weight: 600;
            text-decoration: none;
        }
        
        .error-message {
            background-color: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #dc2626;
            padding: 12px;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>Selamat Datang Kembali 👋</h1>
            <p>Masuk untuk melanjutkan perjalanan liburan Anda</p>
        </div>

        @if ($errors->any())
            <div class="error-message">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="/login">
            @csrf
            
            <div class="form-group">
                <input type="email" name="email" placeholder="Email" 
                       value="{{ old('email') }}" required>
            </div>
            
            <div class="form-group">
                <input type="password" name="password" placeholder="Password" required>
            </div>
            
            <div class="links">
                <a href="/forgot-password">Lupa Password?</a>
            </div>
            
            <button type="submit" class="login-btn">Login</button>
            
            <div class="register-link">
                Belum punya akun? 
                <a href="/register">Daftar sekarang</a>
            </div>
        </form>
    </div>
</body>
</html>
