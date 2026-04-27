<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        body { font-family: Arial, sans-serif; background:#f4f6f8; margin:0; padding:0; }
        .page { min-height:100vh; display:flex; align-items:center; justify-content:center; }
        .card { width:100%; max-width:420px; background:white; border-radius:14px; box-shadow:0 20px 60px rgba(0,0,0,0.08); padding:32px; }
        .card h1 { margin:0 0 24px; font-size:24px; }
        .field { width:100%; margin-bottom:18px; }
        .field label { display:block; margin-bottom:8px; font-weight:600; }
        .field input { width:100%; padding:12px 14px; border:1px solid #d2d6dc; border-radius:10px; }
        .button { width:100%; padding:12px 14px; border:none; border-radius:10px; background:#16a34a; color:white; font-weight:700; cursor:pointer; }
        .small { font-size:0.95rem; color:#475569; }
        .links { display:flex; justify-content:space-between; margin-top:12px; }
        .links a { color:#2563eb; text-decoration:none; }
        .alert { padding:12px 14px; margin-bottom:16px; border-radius:10px; background:#f8fafc; color:#111827; border:1px solid #cbd5e1; }
        .note { margin-bottom:16px; color:#475569; font-size:0.95rem; }
        .error { color:#b91c1c; font-size:0.95rem; margin-top:6px; }
    </style>
</head>
<body>
<div class="page">
    <div class="card">
        <h1>Register</h1>
        <p class="note">silahkan mengisi formulir <strong>pendaftaran</strong>.</p>
        <form method="POST" action="{{ url('/register') }}">
            @csrf
            <div class="field">
                <label for="name">Name</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus>
                @error('name')<div class="error">{{ $message }}</div>@enderror
            </div>
            <div class="field">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required>
                @error('email')<div class="error">{{ $message }}</div>@enderror
            </div>
            <div class="field">
                <label for="password">Password</label>
                <input id="password" type="password" name="password" required>
                @error('password')<div class="error">{{ $message }}</div>@enderror
            </div>
            <div class="field">
                <label for="password_confirmation">Confirm Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required>
            </div>
            <button type="submit" class="button">Register</button>
        </form>
        <div class="links">
            <a href="{{ url('/login') }}">Login</a>
            <a href="{{ url('/forgot-password') }}">Forgot password?</a>
        </div>
    </div>
</div>
</body>
</html>
