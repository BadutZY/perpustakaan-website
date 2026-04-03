<?php

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'perpustakaan');
define('DB_CHARSET', 'utf8mb4');

function getDB()
{
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Koneksi database gagal: ' . $e->getMessage()]);
            exit;
        }
    }
    return $pdo;
}

function startSession()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function getCurrentUser()
{
    startSession();
    return $_SESSION['user'] ?? null;
}

function isLoggedIn()
{
    $user = getCurrentUser();
    return $user !== null;
}

function isAdmin()
{
    $user = getCurrentUser();
    return $user && $user['is_admin'] == 1;
}

function requireLogin()
{
    if (!isLoggedIn()) {
        header('Location: ../login.php');
        exit;
    }
}

function requireAdmin()
{
    if (!isAdmin()) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Akses ditolak']);
        exit;
    }
}

function jsonResponse($success, $message, $data = [])
{
    header('Content-Type: application/json');
    echo json_encode(array_merge(['success' => $success, 'message' => $message], $data));
    exit;
}
