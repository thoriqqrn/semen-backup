<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin | KBIHU Aswaja</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}" type="image/png" sizes="32x32">
    <!-- Font Awesome untuk ikon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Google Fonts - Arabic Style -->
    <link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #1e7e34 0%, #28a745 50%, #20c997 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            position: relative;
            overflow: hidden;
        }

        /* Islamic Pattern Background */
        body::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background-image:
                repeating-linear-gradient(45deg, transparent, transparent 35px, rgba(255,255,255,.03) 35px, rgba(255,255,255,.03) 70px),
                repeating-linear-gradient(-45deg, transparent, transparent 35px, rgba(255,255,255,.03) 35px, rgba(255,255,255,.03) 70px);
            opacity: 0.5;
        }

        /* Decorative Islamic Circles */
        .islamic-decoration {
            position: absolute;
            border-radius: 50%;
            border: 2px solid rgba(255, 255, 255, 0.1);
        }

        .decoration-1 {
            width: 400px;
            height: 400px;
            top: -200px;
            left: -200px;
            animation: float 20s infinite ease-in-out;
        }

        .decoration-2 {
            width: 300px;
            height: 300px;
            bottom: -150px;
            right: -150px;
            animation: float 15s infinite ease-in-out reverse;
        }

        .decoration-3 {
            width: 200px;
            height: 200px;
            top: 50%;
            right: -100px;
            animation: float 18s infinite ease-in-out;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0) rotate(0deg);
            }
            50% {
                transform: translateY(-20px) rotate(180deg);
            }
        }

        .container {
            position: relative;
            z-index: 1;
        }

        .card-login {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            border: none;
            border-radius: 25px;
            box-shadow:
                0 20px 60px rgba(0, 0, 0, 0.15),
                0 0 0 1px rgba(255, 255, 255, 0.1);
            overflow: hidden;
            position: relative;
        }

        /* Islamic Border Pattern */
        .card-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #28a745, #20c997, #28a745);
        }

        .card-body {
            padding: 3rem 2.5rem;
        }

        /* Logo/Icon Container */
        .logo-container {
            width: 100px;
            height: 100px;
            margin: 0 auto 1.5rem;
            background: linear-gradient(135deg, #28a745, #20c997);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 30px rgba(40, 167, 69, 0.3);
            position: relative;
        }

        .logo-container::before {
            content: '';
            position: absolute;
            width: 110px;
            height: 110px;
            border: 2px dashed rgba(40, 167, 69, 0.3);
            border-radius: 50%;
            animation: rotate 30s linear infinite;
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .logo-container i {
            font-size: 2.5rem;
            color: white;
        }

        .card-title {
            font-family: 'Amiri', serif;
            color: #1e7e34;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.05);
        }

        .subtitle {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 2rem;
            font-weight: 300;
        }

        .form-label {
            color: #495057;
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .input-group {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .input-group:focus-within {
            box-shadow: 0 5px 20px rgba(40, 167, 69, 0.2);
            transform: translateY(-2px);
        }

        .input-group-text {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            border: none;
            padding: 0.75rem 1rem;
        }

        .form-control {
            border: none;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            background: #f8f9fa;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            background: white;
            box-shadow: none;
            outline: none;
        }

        .form-control.is-invalid {
            background: #fff5f5;
        }

        .btn-success {
            background: linear-gradient(135deg, #28a745, #20c997);
            border: none;
            border-radius: 12px;
            padding: 0.9rem;
            font-weight: 600;
            font-size: 1rem;
            letter-spacing: 0.5px;
            box-shadow: 0 10px 25px rgba(40, 167, 69, 0.3);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-success::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s ease;
        }

        .btn-success:hover::before {
            left: 100%;
        }

        .btn-success:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(40, 167, 69, 0.4);
            background: linear-gradient(135deg, #20c997, #28a745);
        }

        .btn-success:active {
            transform: translateY(-1px);
        }

        .alert {
            border-radius: 12px;
            border: none;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
            animation: slideDown 0.5s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-success {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            color: #155724;
        }

        .alert-danger {
            background: linear-gradient(135deg, #f8d7da, #f5c6cb);
            color: #721c24;
        }

        .invalid-feedback {
            font-size: 0.85rem;
            margin-top: 0.5rem;
        }

        /* Bismillah Decoration */
        .bismillah {
            font-family: 'Amiri', serif;
            text-align: center;
            color: #28a745;
            font-size: 1.2rem;
            /* Ubah margin-top menjadi margin-bottom untuk memberi jarak ke bawah */
            margin-bottom: 1.5rem; 
            opacity: 0.7;
            font-weight: 700;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .card-body {
                padding: 2rem 1.5rem;
            }

            .card-title {
                font-size: 1.5rem;
            }

            .logo-container {
                width: 80px;
                height: 80px;
            }

            .logo-container i {
                font-size: 2rem;
            }
        }

        /* Loading Animation */
        .btn-success.loading {
            pointer-events: none;
            position: relative;
        }

        .btn-success.loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            top: 50%;
            left: 50%;
            margin-left: -10px;
            margin-top: -10px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <!-- Islamic Decorative Elements -->
    <div class="islamic-decoration decoration-1"></div>
    <div class="islamic-decoration decoration-2"></div>
    <div class="islamic-decoration decoration-3"></div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card card-login">
                    <div class="card-body">

                        <!-- Logo Container -->
                        <div class="logo-container">
                            <i class="fas fa-mosque"></i>
                        </div>
                        <div class="bismillah">
                            ﷽
                        </div>

                        <h2 class="card-title text-center">Login Admin</h2>
                        <p class="subtitle text-center">KBIHU Aswaja</p>

                        @if(session('success'))
                        <div class="alert alert-success text-center">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        </div>
                        @endif

                        @if(session('error'))
                        <div class="alert alert-danger text-center">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                        </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}" id="loginForm">
                            @csrf
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                    <input type="email"
                                           class="form-control @error('email') is-invalid @enderror"
                                           id="email"
                                           name="email"
                                           value="{{ old('email') }}"
                                           placeholder="Masukkan email Anda"
                                           required
                                           autocomplete="email"
                                           autofocus>
                                    @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input type="password"
                                           class="form-control @error('password') is-invalid @enderror"
                                           id="password"
                                           name="password"
                                           placeholder="Masukkan password Anda"
                                           required
                                           autocomplete="current-password">
                                    @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-sign-in-alt me-2"></i>Masuk
                                </button>
                            </div>
                        </form>
                        
                        <!-- === BISMILLAH SUDAH DIPINDAHKAN DARI SINI === -->

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle (popper.js included) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Add loading animation to button on form submit
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const btn = this.querySelector('button[type="submit"]');
            btn.classList.add('loading');
            btn.innerHTML = '';
        });

        // Add smooth focus animation
        const inputs = document.querySelectorAll('.form-control');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.02)';
            });
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });
    </script>
</body>
</html>