<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email</title>
    <style>
        body { font-family: Arial, sans-serif; background:#f4f6f8; margin:0; padding:0; }
        .page { min-height:100vh; display:flex; align-items:center; justify-content:center; }
        .card { width:100%; max-width:420px; background:white; border-radius:14px; box-shadow:0 20px 60px rgba(0,0,0,0.08); padding:32px; }
        .card h1 { margin:0 0 24px; font-size:24px; }
        .field { width:100%; margin-bottom:18px; }
        .field label { display:block; margin-bottom:8px; font-weight:600; }
        .field input { width:100%; padding:12px 14px; border:1px solid #d2d6dc; border-radius:10px; }
        .button { width:100%; padding:12px 14px; border:none; border-radius:10px; background:#16a34a; color:white; font-weight:700; cursor:pointer; }
        .button-secondary { background:#6366f1; }
        .button-secondary:hover { background:#4f46e5; }
        .small { font-size:0.95rem; color:#475569; }
        .links { display:flex; justify-content:space-between; margin-top:12px; }
        .links a { color:#2563eb; text-decoration:none; }
        .alert { padding:12px 14px; margin-bottom:16px; border-radius:10px; }
        .alert-info { background:#dbeafe; color:#1e40af; border:1px solid #93c5fd; }
        .alert-success { background:#dcfce7; color:#15803d; border:1px solid #86efac; }
        .note { margin-bottom:16px; color:#475569; font-size:0.95rem; line-height:1.5; }
        .buttons-group { display:flex; gap:12px; margin-top:20px; }
        .buttons-group button, .buttons-group a { flex:1; }
        .form-group { margin-bottom:20px; }
    </style>
</head>
<body>
<div class="page">
    <div class="card">
        <h1>✉️ Verifikasi Email</h1>

        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif

        <div class="form-group">
            <p class="note">
                <strong>Email belum diverifikasi!</strong><br><br>
                Kami telah mengirimkan link verifikasi ke email Anda di <strong>{{ auth()->user()->email }}</strong>. 
                Klik link tersebut untuk menyelesaikan verifikasi email.
            </p>

            <div class="alert alert-info">
                Link verifikasi akan berlaku selama <strong>24 jam</strong>.
            </div>
        </div>

        <div class="form-group">
            <p class="note">Tidak menerima email?</p>
            <form method="POST" action="{{ route('verification.send') }}" style="margin: 0;">
                @csrf
                <button type="submit" class="button button-secondary">Kirim Ulang Link Verifikasi</button>
            </form>
        </div>

        <hr style="border:none; border-top:1px solid #e5e7eb; margin:24px 0;">

        <div class="links">
            <form method="POST" action="{{ route('logout') }}" style="margin: 0; flex: 1;">
                @csrf
                <button type="submit" style="background:none; border:none; color:#2563eb; cursor:pointer; text-decoration:underline; padding:0;">
                    Logout
                </button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
