<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - POMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8f9fc; height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-card { border: none; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); overflow: hidden; width: 100%; max-width: 420px; background: #fff; }
        .login-header { padding: 40px 40px 20px; text-align: center; }
        .brand-icon { width: 56px; height: 56px; background: linear-gradient(135deg, #4f46e5, #6366f1); border-radius: 14px; display: inline-flex; align-items: center; justify-content: center; color: #fff; font-weight: 700; font-size: 1.5rem; margin-bottom: 1rem; }
        .login-body { padding: 20px 40px 40px; }
        .form-control { border-radius: 8px; padding: 0.75rem 1rem; border: 1px solid #e5e7eb; }
        .form-control:focus { border-color: #4f46e5; box-shadow: 0 0 0 0.25rem rgba(79,70,229,0.1); }
        .btn-primary { background: #4f46e5; border-color: #4f46e5; border-radius: 8px; padding: 0.75rem 1rem; font-weight: 500; }
        .btn-primary:hover { background: #4338ca; border-color: #4338ca; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <div class="brand-icon">P</div>
            <h4 class="fw-bold text-dark mb-1">Selamat Datang</h4>
            <p class="text-muted small">Masuk ke akun POMS Anda</p>
        </div>
        <div class="login-body">
            @if($errors->any())
                <div class="alert alert-danger small rounded-3 py-2">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login.submit') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-medium">Alamat Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus placeholder="admin@contoh.com">
                </div>
                <div class="mb-4">
                    <label class="form-label small fw-medium">Kata Sandi</label>
                    <input type="password" name="password" class="form-control" required placeholder="••••••••">
                </div>
                <div class="mb-4 form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label small" for="remember">Ingat saya</label>
                </div>
                <button type="submit" class="btn btn-primary w-100">Masuk Aplikasi</button>
            </form>
        </div>
    </div>
</body>
</html>
