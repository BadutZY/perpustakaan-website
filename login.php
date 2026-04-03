<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login – Perpustakaan Digital</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.7.12/sweetalert2.all.min.js"></script>
    <link rel="icon" href="IMG/book-logo.png" type="image/x-icon">
    <style>
        :root {
            --primary: #1a2744;
            --secondary: #2563eb;
            --accent: #f59e0b;
            --danger: #ef4444;
            --success: #10b981;
            --bg: #0f172a;
            --card: rgba(255, 255, 255, 0.07);
            --border: rgba(255, 255, 255, 0.12);
            --text: #e2e8f0;
            --muted: #94a3b8;
            --radius: 16px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: var(--bg);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        .bg-blur {
            position: fixed;
            inset: 0;
            z-index: 0;
            background-image: url('CSS/perpus.jpg');
            background-size: cover;
            background-position: center;
            filter: blur(14px) brightness(0.35);
            transform: scale(1.05);
        }

        .particles {
            position: fixed;
            inset: 0;
            z-index: 0;
            overflow: hidden;
            pointer-events: none;
        }

        .particle {
            position: absolute;
            width: 3px;
            height: 3px;
            background: var(--secondary);
            border-radius: 50%;
            opacity: 0;
            animation: float linear infinite;
        }

        @keyframes float {
            0% {
                transform: translateY(100vh) rotate(0);
                opacity: 0;
            }

            10% {
                opacity: 0.6;
            }

            90% {
                opacity: 0.3;
            }

            100% {
                transform: translateY(-10vh) rotate(720deg);
                opacity: 0;
            }
        }

        .card {
            position: relative;
            z-index: 1;
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(24px);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 48px 40px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 32px 64px rgba(0, 0, 0, 0.5);
            animation: slideUp 0.5s ease;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logo {
            text-align: center;
            margin-bottom: 32px;
        }

        .logo-icon {
            width: 72px;
            height: 72px;
            border-radius: 20px;
            background: linear-gradient(135deg, var(--secondary), #7c3aed);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            font-size: 28px;
            color: white;
            box-shadow: 0 8px 24px rgba(37, 99, 235, 0.4);
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                box-shadow: 0 8px 24px rgba(37, 99, 235, 0.4);
            }

            50% {
                box-shadow: 0 8px 32px rgba(37, 99, 235, 0.7);
            }
        }

        .logo h1 {
            color: var(--text);
            font-size: 22px;
            font-weight: 700;
        }

        .logo p {
            color: var(--muted);
            font-size: 13px;
            margin-top: 4px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            color: var(--muted);
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .input-wrap {
            position: relative;
        }

        .input-wrap i.icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
            font-size: 15px;
            pointer-events: none;
        }

        .input-wrap input {
            width: 100%;
            padding: 13px 44px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border);
            border-radius: 10px;
            color: var(--text);
            font-size: 15px;
            transition: all 0.3s;
        }

        .input-wrap input:focus {
            outline: none;
            border-color: var(--secondary);
            background: rgba(37, 99, 235, 0.08);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2);
        }

        .toggle-pw {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
            cursor: pointer;
            font-size: 15px;
            transition: color 0.3s;
        }

        .toggle-pw:hover {
            color: var(--secondary);
        }

        .btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, var(--secondary), #7c3aed);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            letter-spacing: 0.03em;
            margin-top: 4px;
            position: relative;
            overflow: hidden;
        }

        .btn::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(rgba(255, 255, 255, 0.1), transparent);
            pointer-events: none;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(37, 99, 235, 0.5);
        }

        .btn:active {
            transform: translateY(0);
        }

        .btn .spinner {
            display: none;
        }

        .btn.loading .spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
        }

        .btn.loading .btn-text {
            display: none;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .links {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .links a {
            color: var(--secondary);
            font-size: 13px;
            text-decoration: none;
            transition: color 0.3s;
        }

        .links a:hover {
            color: #93c5fd;
            text-decoration: underline;
        }

        .divider {
            position: relative;
            text-align: center;
            margin: 24px 0;
        }

        .divider::before,
        .divider::after {
            content: '';
            position: absolute;
            top: 50%;
            width: 42%;
            height: 1px;
            background: var(--border);
        }

        .divider::before {
            left: 0;
        }

        .divider::after {
            right: 0;
        }

        .divider span {
            color: var(--muted);
            font-size: 12px;
            background: transparent;
            padding: 0 10px;
        }

        .register-btn {
            width: 100%;
            padding: 13px;
            background: transparent;
            border: 1px solid var(--border);
            border-radius: 10px;
            color: var(--text);
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s;
            text-align: center;
            display: block;
            text-decoration: none;
            font-weight: 600;
        }

        .register-btn:hover {
            border-color: var(--secondary);
            color: var(--secondary);
            background: rgba(37, 99, 235, 0.05);
        }

        .back-to-web {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            margin-top: 14px;
            padding: 10px;
            background: transparent;
            border: none;
            color: var(--muted);
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.25s;
        }

        .back-to-web:hover {
            color: var(--text);
            background: rgba(255, 255, 255, 0.05);
        }

        .back-to-web i {
            font-size: 12px;
            transition: transform 0.25s;
        }

        .back-to-web:hover i {
            transform: translateX(-3px);
        }
    </style>
</head>

<body>
    <div class="bg-blur"></div>
    <div class="particles" id="particles"></div>

    <div class="card">
        <div class="logo">
            <div class="logo-icon"><i class="fas fa-book-open"></i></div>
            <h1>Perpustakaan Digital</h1>
            <p>Masuk untuk mengakses koleksi buku</p>
        </div>

        <div class="form-group">
            <label>Username</label>
            <div class="input-wrap">
                <i class="fas fa-user icon"></i>
                <input type="text" id="username" placeholder="Masukkan username" autocomplete="username">
            </div>
        </div>
        <div class="form-group">
            <label>Password</label>
            <div class="input-wrap">
                <i class="fas fa-lock icon"></i>
                <input type="password" id="password" placeholder="Masukkan password" autocomplete="current-password">
                <i class="fas fa-eye toggle-pw" id="toggle-pw"></i>
            </div>
        </div>
        <button class="btn" id="login-btn" onclick="handleLogin()">
            <span class="btn-text"><i class="fas fa-sign-in-alt"></i> &nbsp;Masuk</span>
            <span class="spinner"></span>
        </button>
        <div class="links">
            <a href="reset-password.php">Lupa Password?</a>
        </div>
        <div class="divider"><span>atau</span></div>
        <a href="register.php" class="register-btn"><i class="fas fa-user-plus"></i> &nbsp;Buat Akun Baru</a>
        <a href="index.php" class="back-to-web"><i class="fas fa-arrow-left"></i> Kembali ke Website</a>
    </div>

    <script>
        // Particles
        const container = document.getElementById('particles');
        for (let i = 0; i < 35; i++) {
            const p = document.createElement('div');
            p.className = 'particle';
            p.style.cssText = `left:${Math.random()*100}%;width:${Math.random()*4+1}px;height:${Math.random()*4+1}px;animation-duration:${Math.random()*15+8}s;animation-delay:${Math.random()*8}s;opacity:${Math.random()*0.5}`;
            container.appendChild(p);
        }

        // Password toggle
        document.getElementById('toggle-pw').addEventListener('click', function() {
            const inp = document.getElementById('password');
            const isPass = inp.type === 'password';
            inp.type = isPass ? 'text' : 'password';
            this.className = isPass ? 'fas fa-eye-slash toggle-pw' : 'fas fa-eye toggle-pw';
        });

        document.addEventListener('keydown', e => {
            if (e.key === 'Enter') handleLogin();
        });

        async function handleLogin() {
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value.trim();
            const btn = document.getElementById('login-btn');

            if (!username || !password) {
                Swal.fire({
                    title: 'Peringatan',
                    text: 'Username dan password harus diisi!',
                    icon: 'warning',
                    background: '#1e293b',
                    color: '#e2e8f0',
                    confirmButtonColor: '#2563eb'
                });
                return;
            }

            btn.classList.add('loading');
            try {
                const fd = new FormData();
                fd.append('action', 'login');
                fd.append('username', username);
                fd.append('password', password);

                const res = await fetch('api/auth.php', {
                    method: 'POST',
                    body: fd
                });
                const data = await res.json();

                if (data.success) {
                    Swal.fire({
                        title: 'Selamat Datang!',
                        text: `Halo, ${data.user.username}! ${data.user.is_admin ? '(Admin)' : ''}`,
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false,
                        background: '#1e293b',
                        color: '#e2e8f0'
                    }).then(() => {
                        window.location.href = 'index.php';
                    });
                } else {
                    Swal.fire({
                        title: 'Login Gagal',
                        text: data.message,
                        icon: 'error',
                        background: '#1e293b',
                        color: '#e2e8f0',
                        confirmButtonColor: '#2563eb'
                    });
                }
            } catch (e) {
                Swal.fire({
                    title: 'Error',
                    text: 'Terjadi kesalahan koneksi!',
                    icon: 'error',
                    background: '#1e293b',
                    color: '#e2e8f0',
                    confirmButtonColor: '#2563eb'
                });
            }
            btn.classList.remove('loading');
        }
    </script>
</body>

</html>