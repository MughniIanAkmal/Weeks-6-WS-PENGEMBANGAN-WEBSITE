<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Selamat Datang - POS Toko</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f1f5f9; color: #1e293b; margin: 0; padding: 24px; }
        .card { max-width: 520px; margin: 0 auto; background: #ffffff; border-radius: 10px; border: 1px solid #e2e8f0; padding: 32px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        .header { text-align: center; margin-bottom: 24px; }
        .logo { font-size: 24px; font-weight: bold; color: #0284c7; }
        .badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; background: #e0f2fe; color: #0369a1; }
        .btn { display: inline-block; background-color: #0284c7; color: #ffffff !important; text-decoration: none; padding: 10px 24px; border-radius: 6px; font-weight: 600; margin-top: 16px; }
        .footer { text-align: center; margin-top: 32px; font-size: 12px; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            <div class="logo">POS TOKO</div>
            <h2 style="margin: 8px 0 0 0; color: #0f172a;">Selamat Datang, {{ $user->name }}!</h2>
            <p style="margin: 4px 0 0 0; color: #64748b; font-size: 14px;">Akun Anda berhasil didaftarkan ke sistem.</p>
        </div>

        <div style="background-color: #f8fafc; border-radius: 8px; padding: 16px; margin-bottom: 20px;">
            <p style="margin: 0 0 8px 0; font-size: 14px;"><strong>Email:</strong> {{ $user->email }}</p>
            <p style="margin: 0 0 8px 0; font-size: 14px;"><strong>Hak Akses / Role:</strong> <span class="badge">{{ strtoupper($user->role) }}</span></p>
            <p style="margin: 0; font-size: 14px;"><strong>Tanggal Registrasi:</strong> {{ now()->format('d M Y, H:i') }}</p>
        </div>

        <p style="font-size: 14px; line-height: 1.6; color: #475569;">
            Dengan akun ini, Anda dapat mengakses fitur-fitur POS Toko sesuai dengan hak akses yang diberikan. 
            Silakan masuk dan mulai kelola katalog produk, kasir, atau pelaporan bisnis Anda.
        </p>

        <div style="text-align: center;">
            <a href="{{ url('/login') }}" class="btn">Masuk ke Aplikasi</a>
        </div>

        <div class="footer">
            <p style="margin: 0;">Sistem Point of Sale Terintegrasi © {{ date('Y') }} POS Toko</p>
        </div>
    </div>
</body>
</html>
