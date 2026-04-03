<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar – Perpustakaan Digital</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.7.12/sweetalert2.all.min.js"></script>
    <link rel="icon" href="IMG/book-logo.png" type="image/x-icon">
    <style>
        :root {
            --primary: #1a2744;
            --secondary: #2563eb;
            --accent: #f59e0b;
            --bg: #0f172a;
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
            padding: 24px;
        }

        .bg-blur {
            position: fixed;
            inset: 0;
            z-index: 0;
            background-image: url('CSS/perpus.jpg');
            background-size: cover;
            background-position: center;
            filter: blur(14px) brightness(0.3);
            transform: scale(1.05);
        }

        .card {
            position: relative;
            z-index: 1;
            background: rgba(15, 23, 42, 0.88);
            backdrop-filter: blur(24px);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 44px 40px;
            width: 100%;
            max-width: 440px;
            box-shadow: 0 32px 64px rgba(0, 0, 0, 0.5);
            animation: slideUp 0.5s ease;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px)
            }

            to {
                opacity: 1;
                transform: translateY(0)
            }
        }

        .logo {
            text-align: center;
            margin-bottom: 28px;
        }

        .logo-icon {
            width: 64px;
            height: 64px;
            border-radius: 18px;
            background: linear-gradient(135deg, #10b981, #2563eb);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 14px;
            font-size: 26px;
            color: white;
            box-shadow: 0 8px 24px rgba(16, 185, 129, 0.4);
        }

        .logo h1 {
            color: var(--text);
            font-size: 20px;
            font-weight: 700;
        }

        .logo p {
            color: var(--muted);
            font-size: 13px;
            margin-top: 4px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            color: var(--muted);
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 7px;
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
            font-size: 14px;
            pointer-events: none;
        }

        .input-wrap input {
            width: 100%;
            padding: 12px 44px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border);
            border-radius: 10px;
            color: var(--text);
            font-size: 14px;
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
            font-size: 14px;
            transition: color 0.3s;
        }

        .toggle-pw:hover {
            color: var(--secondary);
        }

        .strength-bar {
            display: flex;
            gap: 4px;
            margin-top: 6px;
        }

        .strength-bar span {
            height: 3px;
            flex: 1;
            border-radius: 4px;
            background: rgba(255, 255, 255, 0.1);
            transition: background 0.3s;
        }

        .hint {
            color: var(--muted);
            font-size: 11px;
            margin-top: 5px;
        }

        .btn {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #10b981, #2563eb);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
            margin-top: 4px;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(16, 185, 129, 0.4);
        }

        .btn .spinner {
            display: none;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
        }

        .btn.loading .spinner {
            display: inline-block;
        }

        .btn.loading .btn-text {
            display: none;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg)
            }
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: var(--secondary);
            font-size: 13px;
            text-decoration: none;
            transition: color 0.3s;
        }

        .back-link:hover {
            color: #93c5fd;
            text-decoration: underline;
        }

        .back-to-web {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            margin-top: 10px;
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
    <div class="card">
        <div class="logo">
            <div class="logo-icon"><i class="fas fa-user-plus"></i></div>
            <h1>Buat Akun Baru</h1>
            <p>Bergabung dengan Perpustakaan Digital</p>
        </div>
        <div class="form-group">
            <label>Username</label>
            <div class="input-wrap">
                <i class="fas fa-user icon"></i>
                <input type="text" id="username" placeholder="Min. 3 karakter" autocomplete="username">
            </div>
        </div>
        <div class="form-group">
            <label>Email (opsional)</label>
            <div class="input-wrap">
                <i class="fas fa-envelope icon"></i>
                <input type="email" id="email" placeholder="email@contoh.com" autocomplete="email">
            </div>
        </div>
        <div class="form-group">
            <label>Password</label>
            <div class="input-wrap">
                <i class="fas fa-lock icon"></i>
                <input type="password" id="password" placeholder="Min. 6 karakter" autocomplete="new-password" oninput="checkStrength(this.value)">
                <i class="fas fa-eye toggle-pw" id="toggle-pw1"></i>
            </div>
            <div class="strength-bar"><span id="s1"></span><span id="s2"></span><span id="s3"></span><span id="s4"></span></div>
            <div class="hint" id="strength-hint">Masukkan password</div>
        </div>
        <div class="form-group">
            <label>Konfirmasi Password</label>
            <div class="input-wrap">
                <i class="fas fa-lock icon"></i>
                <input type="password" id="confirm-password" placeholder="Ulangi password" autocomplete="new-password">
                <i class="fas fa-eye toggle-pw" id="toggle-pw2"></i>
            </div>
        </div>
        <button class="btn" id="reg-btn" onclick="handleRegister()">
            <span class="btn-text"><i class="fas fa-check"></i> &nbsp;Daftar Sekarang</span>
            <span class="spinner"></span>
        </button>
        <a href="login.php" class="back-link"><i class="fas fa-arrow-left"></i> &nbsp;Sudah punya akun? Login</a>
        <a href="index.php" class="back-to-web"><i class="fas fa-arrow-left"></i> Kembali ke Website</a>
    </div>

    <script>
        function togglePw(inputId, iconId) {
            const inp = document.getElementById(inputId);
            const ic = document.getElementById(iconId);
            const show = inp.type === 'password';
            inp.type = show ? 'text' : 'password';
            ic.className = show ? 'fas fa-eye-slash toggle-pw' : 'fas fa-eye toggle-pw';
        }
        document.getElementById('toggle-pw1').addEventListener('click', () => togglePw('password', 'toggle-pw1'));
        document.getElementById('toggle-pw2').addEventListener('click', () => togglePw('confirm-password', 'toggle-pw2'));

        function checkStrength(val) {
            const bars = ['s1', 's2', 's3', 's4'];
            const hint = document.getElementById('strength-hint');
            let score = 0;
            if (val.length >= 6) score++;
            if (val.length >= 10) score++;
            if (/[A-Z]/.test(val) && /[0-9]/.test(val)) score++;
            if (/[^A-Za-z0-9]/.test(val)) score++;
            const colors = ['#ef4444', '#f59e0b', '#10b981', '#2563eb'];
            const hints = ['Terlalu lemah', 'Lemah', 'Kuat', 'Sangat kuat'];
            bars.forEach((id, i) => {
                document.getElementById(id).style.background = i < score ? colors[score - 1] : 'rgba(255,255,255,0.1)';
            });
            hint.textContent = val ? hints[Math.min(score - 1, 3)] || 'Terlalu lemah' : 'Masukkan password';
            hint.style.color = score > 0 ? colors[Math.min(score - 1, 3)] : '#94a3b8';
        }

        async function handleRegister() {
            const username = document.getElementById('username').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const confirm = document.getElementById('confirm-password').value;
            const btn = document.getElementById('reg-btn');

            if (!username || !password || !confirm) {
                Swal.fire({
                    title: 'Peringatan',
                    text: 'Username dan password wajib diisi!',
                    icon: 'warning',
                    background: '#1e293b',
                    color: '#e2e8f0',
                    confirmButtonColor: '#2563eb'
                });
                return;
            }
            if (password !== confirm) {
                Swal.fire({
                    title: 'Tidak Cocok',
                    text: 'Password dan konfirmasi tidak sama!',
                    icon: 'error',
                    background: '#1e293b',
                    color: '#e2e8f0',
                    confirmButtonColor: '#2563eb'
                });
                return;
            }

            btn.classList.add('loading');
            try {
                const fd = new FormData();
                fd.append('action', 'register');
                fd.append('username', username);
                fd.append('email', email);
                fd.append('password', password);
                fd.append('confirm_password', confirm);

                const res = await fetch('api/auth.php', {
                    method: 'POST',
                    body: fd
                });
                const data = await res.json();

                if (data.success) {
                    Swal.fire({
                            title: 'Berhasil!',
                            text: data.message,
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false,
                            background: '#1e293b',
                            color: '#e2e8f0'
                        })
                        .then(() => {
                            window.location.href = 'login.php';
                        });
                } else {
                    Swal.fire({
                        title: 'Gagal',
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
                    text: 'Terjadi kesalahan!',
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