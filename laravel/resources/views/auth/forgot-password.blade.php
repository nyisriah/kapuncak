<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <style>
        body { font-family: Arial, sans-serif; background:#f4f6f8; margin:0; padding:0; }
        .page { min-height:100vh; display:flex; align-items:center; justify-content:center; }
        .card { width:100%; max-width:420px; background:white; border-radius:14px; box-shadow:0 20px 60px rgba(0,0,0,0.08); padding:32px; }
        .card h1 { margin:0 0 24px; font-size:24px; }
        .field { width:100%; margin-bottom:18px; }
        .field label { display:block; margin-bottom:8px; font-weight:600; }
        .field input { width:100%; padding:12px 14px; border:1px solid #d2d6dc; border-radius:10px; }
        .button { width:100%; padding:12px 14px; border:none; border-radius:10px; background:#f59e0b; color:white; font-weight:700; cursor:pointer; }
        .links { display:flex; justify-content:space-between; margin-top:12px; }
        .links a { color:#2563eb; text-decoration:none; }
        .alert { padding:12px 14px; margin-bottom:16px; border-radius:10px; background:#f8fafc; color:#111827; border:1px solid #cbd5e1; }
        .error { color:#b91c1c; font-size:0.95rem; margin-top:6px; }
    </style>
</head>
<body>
<div class="page">
    <div class="card">
        <h1>Forgot Password</h1>
        @if(session('status'))
            <div class="alert">{{ session('status') }}</div>
        @endif
        <form method="POST" action="{{ url('/forgot-password') }}">
            @csrf
            <div class="field">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
                @error('email')<div class="error">{{ $message }}</div>@enderror
            </div>
            <button type="submit" class="button">Send Reset Link</button>
        </form>
        <div class="links">
            <a href="{{ url('/login') }}">Login</a>
            <a href="{{ url('/register') }}">Register</a>
        </div>
    </div>
</div>
</body>
</html>
