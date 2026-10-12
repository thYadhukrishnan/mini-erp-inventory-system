<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login – Mini ERP</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #0f172a;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-wrap {
            width: 100%;
            max-width: 420px;
            padding: 1rem;
        }
        .login-card {
            background: #1e293b;
            border: 1px solid rgba(99,102,241,.2);
            border-radius: 18px;
            padding: 2.5rem 2rem;
        }
        .brand-icon {
            width: 52px; height: 52px;
            border-radius: 14px;
            background: linear-gradient(135deg, #6366f1, #818cf8);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem; color: #fff;
            margin: 0 auto 1rem;
        }
        .form-control {
            background: #334155;
            border: 1px solid rgba(99,102,241,.2);
            color: #e2e8f0;
            border-radius: 8px;
        }
        .form-control:focus {
            background: #334155;
            border-color: #6366f1;
            color: #e2e8f0;
            box-shadow: 0 0 0 3px rgba(99,102,241,.15);
        }
        .form-label { color: #94a3b8; font-size: .85rem; font-weight: 500; }
        .btn-login {
            background: #6366f1;
            color: #fff; border: none;
            border-radius: 8px;
            padding: .65rem;
            font-weight: 600;
            transition: background .2s;
        }
        .btn-login:hover { background: #4f46e5; color: #fff; }
        .text-muted { color: #64748b !important; }
    </style>
</head>
<body>
<div class="login-wrap">
    <div class="login-card">
        <div class="text-center mb-4">
            <div class="brand-icon"><i class="bi bi-boxes"></i></div>
            <h1 class="h5 fw-bold text-white mb-1">Mini ERP</h1>
            <p class="text-muted" style="font-size:.85rem">Sign in to your account</p>
        </div>

        @if($errors->any())
        <div class="alert alert-danger py-2" style="font-size:.85rem;border-radius:8px">
            <i class="bi bi-exclamation-triangle me-1"></i>
            {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" id="email" name="email"
                       class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}" required autofocus>
            </div>
            <div class="mb-4">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password"
                       class="form-control @error('password') is-invalid @enderror"
                       required>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                <label class="form-check-label text-muted" for="remember" style="font-size:.85rem">Remember me</label>
            </div>
            <button type="submit" class="btn btn-login w-100">
                <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
            </button>
        </form>

        <div class="text-center mt-4 text-muted" style="font-size:.78rem">
            <i class="bi bi-info-circle me-1"></i>
            Demo: <code style="color:#818cf8">admin@example.com</code> / <code style="color:#818cf8">password</code>
        </div>
    </div>
</div>
</body>
</html>
