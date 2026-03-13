<!doctype html>
<html lang="fr" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion | Le Fouquet</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <style>
        :root {
            --brand-blue: #2563eb;
            --light-bg: #f8fafc;
        }

        body {
            min-height: 100vh;
            background:
                radial-gradient(circle at 10% 20%, rgba(37, 99, 235, 0.18), transparent 32%),
                radial-gradient(circle at 90% 80%, rgba(219, 234, 254, 0.95), transparent 34%),
                var(--light-bg);
            color: #1e293b;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .login-card {
            max-width: 460px;
            margin: 8vh auto;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 2rem;
            box-shadow: 0 20px 40px -20px rgba(37, 99, 235, 0.35);
        }

        .brand-title {
            font-family: 'Playfair Display', serif;
            color: #1e3a8a;
        }

        .form-control {
            background: #fff;
            border-color: #cbd5e1;
            color: #0f172a;
        }

        .form-control:focus {
            background: #fff;
            color: #0f172a;
            border-color: var(--brand-blue);
            box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.15);
        }

        .btn-primary {
            --bs-btn-bg: var(--brand-blue);
            --bs-btn-border-color: var(--brand-blue);
            --bs-btn-color: #fff;
            --bs-btn-hover-color: #fff;
            --bs-btn-hover-bg: #1d4ed8;
            --bs-btn-hover-border-color: #1d4ed8;
            font-weight: 700;
        }
    </style>
</head>
<body>
<div class="container py-4">
    <div class="card login-card">
        <div class="card-body p-4 p-md-5">
            <h1 class="h2 text-center mb-2 brand-title">Le Fouquet</h1>
            <p class="text-primary text-center mb-4 fw-bold" style="font-size: 11px; letter-spacing: .14em; text-transform: uppercase;">Interface de gestion live</p>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login.perform') }}">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Mot de passe</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                <div class="mb-3 form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label" for="remember">Se souvenir de moi</label>
                </div>
                <button class="btn btn-primary w-100 py-2" type="submit">Se connecter</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
