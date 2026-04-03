<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password – Perpustakaan Digital</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.7.12/sweetalert2.all.min.js"></script>
    <link rel="icon" href="IMG/book-logo.png" type="image/x-icon">
    <style>
        :root {
            --secondary: #2563eb;
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
            max-width: 420px;
            box-shadow: 0 32px 64px rgba(0, 0, 0, 0.5);
        }

        .logo {
            text-align: center;
            margin-bottom: 28px;
        }

        .logo-icon {
            width: 64px;
            height: 64px;
            border-radius: 18px;
            background: linear-gradient(135deg, #f59e0b, #ef4444);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 14px;
            font-size: 26px;
            color: white;
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

        .info-box {
            background: rgba(245, 158, 11, 0.1);
            border: 1px solid rgba(245, 158, 11, 0.3);
            border-radius: 10px;
            padding: 12px 16px;
            margin-bottom: 20px;
            color: #fcd34d;
            font-size: 13px;
            display: flex;
            gap: 10px;
            align-items: flex-start;
        }

        .btn {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #f59e0b, #ef4444);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 4px;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(245, 158, 11, 0.4);
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
            <div class="logo-icon"><i class="fas fa-key"></i></div>
            <h1>Reset Password</h1>
            <p>Ganti password akun Anda</p>
        </div>
        <div class="info-box"><i class="fas fa-info-circle"></i><span>Masukkan username dan password baru Anda. Pastikan Anda ingat username yang terdaftar.</span></div>
        <div class="form-group">
            <label>Username</label>
            <div class="input-wrap">
                <i class="fas fa-user icon"></i>
                <input type="text" id="username" placeholder="Masukkan username terdaftar">
            </div>
        </div>
        <div class="form-group">
            <label>Password Baru</label>
            <div class="input-wrap">
                <i class="fas fa-lock icon"></i>
                <input type="password" id="new-password" placeholder="Min. 6 karakter">
                <i class="fas fa-eye toggle-pw" id="toggle-pw1"></i>
            </div>
        </div>
        <div class="form-group">
            <label>Konfirmasi Password</label>
            <div class="input-wrap">
                <i class="fas fa-lock icon"></i>
                <input type="password" id="confirm-password" placeholder="Ulangi password baru">
                <i class="fas fa-eye toggle-pw" id="toggle-pw2"></i>
            </div>
        </div>
        <button class="btn" onclick="handleReset()"><i class="fas fa-save"></i> &nbsp;Simpan Password Baru</button>
        <a href="login.php" class="back-link"><i class="fas fa-arrow-left"></i> &nbsp;Kembali ke Login</a>
        <a href="index.php" class="back-to-web"><i class="fas fa-arrow-left"></i> Kembali ke Website</a>
    </div>
    <script>
        document.getElementById('toggle-pw1').addEventListener('click', function() {
            const inp = document.getElementById('new-password');
            inp.type = inp.type === 'password' ? 'text' : 'password';
            this.className = inp.type === 'text' ? 'fas fa-eye-slash toggle-pw' : 'fas fa-eye toggle-pw';
        });
        document.getElementById('toggle-pw2').addEventListener('click', function() {
            const inp = document.getElementById('confirm-password');
            inp.type = inp.type === 'password' ? 'text' : 'password';
            this.className = inp.type === 'text' ? 'fas fa-eye-slash toggle-pw' : 'fas fa-eye toggle-pw';
        });

        async function handleReset() {
            const username = document.getElementById('username').value.trim();
            const newPw = document.getElementById('new-password').value;
            const confirm = document.getElementById('confirm-password').value;

            if (!username || !newPw || !confirm) {
                return Swal.fire({
                    title: 'Peringatan',
                    text: 'Semua kolom harus diisi!',
                    icon: 'warning',
                    background: '#1e293b',
                    color: '#e2e8f0',
                    confirmButtonColor: '#2563eb'
                });
            }
            if (newPw !== confirm) {
                return Swal.fire({
                    title: 'Tidak Cocok',
                    text: 'Password tidak sama!',
                    icon: 'error',
                    background: '#1e293b',
                    color: '#e2e8f0',
                    confirmButtonColor: '#2563eb'
                });
            }
            if (newPw.length < 6) {
                return Swal.fire({
                    title: 'Terlalu Pendek',
                    text: 'Password minimal 6 karakter!',
                    icon: 'warning',
                    background: '#1e293b',
                    color: '#e2e8f0',
                    confirmButtonColor: '#2563eb'
                });
            }

            try {
                const fd = new FormData();
                fd.append('action', 'reset_password');
                fd.append('username', username);
                fd.append('password', newPw);
                const res = await fetch('api/auth.php', {
                    method: 'POST',
                    body: fd
                });
                const data = await res.json();
                if (data.success) {
                    Swal.fire({
                            title: 'Berhasil!',
                            text: 'Password berhasil diubah. Silakan login.',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false,
                            background: '#1e293b',
                            color: '#e2e8f0'
                        })
                        .then(() => window.location.href = 'login.php');
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
        }
    </script>
</body>

</html>