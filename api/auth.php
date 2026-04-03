<?php

require_once '../config/database.php';
header('Content-Type: application/json');
startSession();

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'login':
        handleLogin();
        break;
    case 'register':
        handleRegister();
        break;
    case 'logout':
        handleLogout();
        break;
    case 'check':
        checkSession();
        break;
    case 'reset_password':
        handleResetPassword_func();
        break;
    default:
        jsonResponse(false, 'Action tidak valid');
}

function handleLogin()
{
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!$username || !$password) {
        jsonResponse(false, 'Username dan password harus diisi!');
    }

    $db = getDB();
    $stmt = $db->prepare("SELECT id, username, password, is_admin FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password'])) {
        jsonResponse(false, 'Username atau password salah!');
    }

    $_SESSION['user'] = [
        'id'       => $user['id'],
        'username' => $user['username'],
        'is_admin' => $user['is_admin'],
    ];

    jsonResponse(true, 'Login berhasil', [
        'user' => [
            'id'       => $user['id'],
            'username' => $user['username'],
            'is_admin' => (bool)$user['is_admin'],
        ]
    ]);
}

function handleRegister()
{
    $username        = trim($_POST['username'] ?? '');
    $password        = trim($_POST['password'] ?? '');
    $confirmPassword = trim($_POST['confirm_password'] ?? '');
    $email           = trim($_POST['email'] ?? '');

    if (!$username || !$password || !$confirmPassword) {
        jsonResponse(false, 'Semua kolom harus diisi!');
    }

    if (strlen($username) < 3) {
        jsonResponse(false, 'Username minimal 3 karakter!');
    }

    if (strlen($password) < 6) {
        jsonResponse(false, 'Password minimal 6 karakter!');
    }

    if ($password !== $confirmPassword) {
        jsonResponse(false, 'Password dan konfirmasi password tidak cocok!');
    }

    $db = getDB();
    $stmt = $db->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        jsonResponse(false, 'Username sudah terdaftar! Gunakan username lain.');
    }

    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $db->prepare("INSERT INTO users (username, password, email, is_admin) VALUES (?, ?, ?, 0)");
    $stmt->execute([$username, $hashedPassword, $email]);

    jsonResponse(true, 'Registrasi berhasil! Silakan login.');
}

function handleLogout()
{
    session_destroy();
    jsonResponse(true, 'Logout berhasil');
}

function checkSession()
{
    if (isLoggedIn()) {
        $user = getCurrentUser();
        jsonResponse(true, 'Session aktif', ['user' => $user]);
    } else {
        jsonResponse(false, 'Tidak ada session aktif');
    }
}

function handleResetPassword_func()
{
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!$username || !$password) {
        jsonResponse(false, 'Semua kolom harus diisi!');
    }
    if (strlen($password) < 6) {
        jsonResponse(false, 'Password minimal 6 karakter!');
    }

    $db = getDB();
    $stmt = $db->prepare("SELECT id FROM users WHERE username = ? AND is_admin = 0");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if (!$user) {
        jsonResponse(false, 'Username tidak ditemukan atau tidak dapat direset!');
    }

    $hashed = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->execute([$hashed, $user['id']]);

    jsonResponse(true, 'Password berhasil diubah!');
}
