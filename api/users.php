<?php

require_once '../config/database.php';
header('Content-Type: application/json');
startSession();

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'list':
        listUsers();
        break;
    case 'edit':
        editUser();
        break;
    case 'delete':
        deleteUser();
        break;
    default:
        jsonResponse(false, 'Action tidak valid');
}

function listUsers()
{
    if (!isAdmin()) jsonResponse(false, 'Akses ditolak');

    $db = getDB();
    $sql = "SELECT u.id, u.username, u.email, u.is_admin, u.created_at,
                   COUNT(CASE WHEN br.status IN ('borrowed','overdue') THEN 1 END) AS active_borrows,
                   COUNT(br.id) AS total_borrows
            FROM users u
            LEFT JOIN borrows br ON u.id = br.user_id
            GROUP BY u.id
            ORDER BY u.is_admin DESC, u.username ASC";
    $stmt = $db->query($sql);
    jsonResponse(true, 'OK', ['users' => $stmt->fetchAll()]);
}

function editUser()
{
    if (!isAdmin()) jsonResponse(false, 'Akses ditolak');

    $id          = (int)($_POST['id'] ?? 0);
    $username    = trim($_POST['username'] ?? '');
    $password    = trim($_POST['password'] ?? '');
    $email       = trim($_POST['email'] ?? '');

    if (!$id || !$username) jsonResponse(false, 'Data tidak lengkap!');

    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch();
    if (!$user) jsonResponse(false, 'Pengguna tidak ditemukan!');

    $stmt = $db->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
    $stmt->execute([$username, $id]);
    if ($stmt->fetch()) jsonResponse(false, 'Username sudah digunakan!');

    if ($password) {
        if (strlen($password) < 6) jsonResponse(false, 'Password minimal 6 karakter!');
        $hashed = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $db->prepare("UPDATE users SET username=?, email=?, password=? WHERE id=?");
        $stmt->execute([$username, $email, $hashed, $id]);
    } else {
        $stmt = $db->prepare("UPDATE users SET username=?, email=? WHERE id=?");
        $stmt->execute([$username, $email, $id]);
    }

    jsonResponse(true, "Data pengguna \"$username\" berhasil diperbarui!");
}

function deleteUser()
{
    if (!isAdmin()) jsonResponse(false, 'Akses ditolak');

    $id      = (int)($_POST['id'] ?? 0);
    $current = getCurrentUser();

    if (!$id) jsonResponse(false, 'ID tidak valid');
    if ($id === (int)$current['id']) jsonResponse(false, 'Anda tidak dapat menghapus akun Anda sendiri!');

    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch();

    if (!$user) jsonResponse(false, 'Pengguna tidak ditemukan!');
    if ($user['is_admin'] && $user['username'] === 'Fritzy') {
        jsonResponse(false, 'Admin utama tidak dapat dihapus!');
    }

    $stmt = $db->prepare("SELECT COUNT(*) FROM borrows WHERE user_id = ? AND status IN ('borrowed','overdue')");
    $stmt->execute([$id]);
    if ($stmt->fetchColumn() > 0) {
        jsonResponse(false, 'Pengguna ini masih memiliki buku yang dipinjam!');
    }

    $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$id]);

    jsonResponse(true, "Pengguna \"{$user['username']}\" berhasil dihapus!");
}
