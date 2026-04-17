<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caderneta Digital</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <style>
        body {
            background: linear-gradient(135deg, #f5f5f5, #e4e4e4);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
        }

        .login-card {
            backdrop-filter: blur(15px);
            background: rgba(247, 247, 247, 0.36);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
            overflow: hidden;
            width: 100%;
            max-width: 420px;
            animation: fadeIn 0.6s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-header {
            text-align: center;
            padding: 2rem 1.5rem;
            background: transparent;
        }

        .login-header i {
            font-size: 3rem;
            color: #4f46e5;
        }

        .login-header h3 {
            margin-top: 10px;
            font-weight: 600;
        }

        .login-body {
            padding: 1.5rem 2rem 2rem;
        }

        .form-control {
            border-radius: 10px;
            padding: 10px 12px;
            border: 1px solid #ddd;
        }

        .input-group-text {
            border-radius: 10px 0 0 10px;
            background: #f1f1f1;
        }

        .btn-login {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }

        .links a {
            font-size: 0.9rem;
            color: #4f46e5;
        }

        .links a:hover {
            text-decoration: underline;
        }

        .alert {
            border-radius: 10px;
        }
        
    </style>
</head>
<body>

<div class="login-card">

    <div class="login-header">
        <i class="bi bi-journal-bookmark-fill"></i>
        <h3>Caderneta Digital</h3>
        <small class="text-muted">Controle de vendas</small>
    </div>

    <div class="login-body">

        @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $error)
                    <div><i class="bi bi-exclamation-triangle-fill"></i> {{ $error }}</div>
                @endforeach
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label">E-mail</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Senha</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" class="form-control" name="password" required>
                </div>
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" name="remember">
                <label class="form-check-label">Lembrar-me</label>
            </div>

            <button type="submit" class="btn btn-primary btn-login w-100">
                <i class="bi bi-box-arrow-in-right"></i> Entrar
            </button>

            <div class="text-center mt-3 links">
                <a href="{{ route('password.request') }}">Esqueceu a senha?</a><br>
                <a href="{{ route('register') }}">Criar conta</a>
            </div>
        </form>

    </div>
</div>

</body>
</html>